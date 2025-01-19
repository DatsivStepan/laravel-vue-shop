<?php

namespace Modules\Payments\Services\PaymentMethods;

use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Subscriptions\RecurringPeriod;
use App\Classes\Enum\Subscriptions\SubscriptionStatus;
use App\Models\Orders;
use App\Models\Refund;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Methods\AuthorizeMethod;
use Modules\Payments\Services\Enum\Authorize\AuthorizeCurringPeriod;
use Modules\Payments\Services\PaymentMethods\Authorize\Enum\AuthorizeSubscriptionStatus;
use Modules\Payments\Services\PaymentProvider;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\AnetApiResponseType;
use net\authorize\api\contract\v1\ARBCreateSubscriptionRequest;
use net\authorize\api\contract\v1\ARBGetSubscriptionStatusRequest;
use net\authorize\api\contract\v1\ARBSubscriptionType;
use net\authorize\api\contract\v1\CreateCustomerProfileFromTransactionRequest;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\contract\v1\CustomerProfileIdType;
use net\authorize\api\contract\v1\ExtendedAmountType;
use net\authorize\api\contract\v1\GetTransactionDetailsRequest;
use net\authorize\api\contract\v1\LineItemType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\OpaqueDataType;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1\PaymentScheduleType;
use net\authorize\api\contract\v1\PaymentScheduleType\IntervalAType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\ARBCreateSubscriptionController;
use net\authorize\api\controller\ARBGetSubscriptionStatusController;
use net\authorize\api\controller\CreateCustomerProfileFromTransactionController;
use net\authorize\api\controller\CreateTransactionController;
use net\authorize\api\controller\GetTransactionDetailsController;

class Authorize implements PaymentProvider
{

    private const WITHOUT_END_DATE = 9999;
    private const MINUS_CYCLES = 1;
    /**
     * @var PaymentMethod
     */
    private $paymentMethod;
    /**
     * @var MerchantAuthenticationType
     */
    private $client;
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * Authorize constructor.
     * @param PaymentMethod $paymentMethod
     */
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->client = $this->getClient($paymentMethod);
        $this->subscriptionService = app(SubscriptionService::class);
    }

    /**
     * @param Orders $order
     * @param array $extraData
     * @return array
     */
    public function makeOrder(Orders $order, $extraData = []): array
    {
        $payload = $order->toArray();

        // Add the payment data to a paymentType object
        $payment = $this->createPayment($extraData);
        // Create order information
        $orderType = $this->crateOrder($payload['id'], $payload['note']);
        $lineItems = $this->createLineItems($payload['products']);
        $tax = $this->createExtendedAmountType($payload['tax']);
        // Set the customer's identifying information
        $customerData = $this->createCustomerDataType($order->user_id, $order->user->email);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($payload['amount']);
        $transactionRequestType->setOrder($orderType);
        $transactionRequestType->setPayment($payment);
        $transactionRequestType->setCurrencyCode($payload['currency']);
        $transactionRequestType->setLineItems($lineItems);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->setTax($tax);

        return $this->createTransactionRequest($transactionRequestType);
    }

    /**
     * @param $payload
     * @return array
     * @throws Exception
     */
    public function subscribe($payload): array
    {
        $order = $this->prepareOrderForSubscription($payload);
        $extraData = $payload['extra_data'];
        $orderPayment = $this->makeOrder($order, $extraData);
        if (!$orderPayment['status']) {
            throw new Exception($orderPayment['error_message']);
        } else {
            if ($orderPayment['response_code'] !== 1) {
                //TODO admin should approve transaction
            }
        }

        //Create Profile
        $customerProfile = $this->createCustomerProfile($orderPayment['transaction_id']);

        // Subscription Type Info
        $subscription = new ARBSubscriptionType();
        $subscription->setName($payload['name']);
        $subscription->setAmount($payload['total']);

        $paymentSchedule = $this->createPaymentSchedule($payload['cycles'], $payload['recurring_period']);
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setProfile($customerProfile);

        $order = $this->crateOrder($payload['order_id'], $payload['name']);
        $subscription->setOrder($order);

        return $this->createSubscriptionRequest($subscription);
    }

    /**
     * @param array $extraData
     * @return PaymentType
     */
    private function createPayment(array $extraData): PaymentType
    {
        $op = new OpaqueDataType();
        $op->setDataDescriptor($extraData['authorize']['dataDescriptor']);
        $op->setDataValue($extraData['authorize']['dataValue']);

        // Add the payment data to a paymentType object
        $payment = new PaymentType();
        $payment->setOpaqueData($op);

        return $payment;
    }

    /**
     * @param array $products
     * @return array
     */
    private function createLineItems(array $products): array
    {
        $lineItems = [];

        foreach ($products as $product) {
            $lineItem = new LineItemType();
            $lineItem->setItemId($product['id']);
            $lineItem->setName($product['name']);
            $lineItem->setQuantity($product['quantity']);
            $lineItem->setUnitPrice($product['discount_price']);
            $lineItem->setTaxable(boolval($product['tax']));

            $lineItems[] = $lineItem;
        }

        return $lineItems;
    }

    /**
     * @param $userID
     * @param string $email
     * @return CustomerDataType
     */
    private function createCustomerDataType($userID, string $email): CustomerDataType
    {
        $customerID = tenant('id') . 'id' . $userID;
        // Set the customer's identifying information
        $customerData = new CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($customerID);
        $customerData->setEmail($email);

        return $customerData;
    }

    /**
     * @param $amount
     * @return ExtendedAmountType
     */
    private function createExtendedAmountType($amount): ExtendedAmountType
    {
        $tax = new ExtendedAmountType();
        $tax->setAmount($amount);

        return $tax;
    }

    /**
     * @param TransactionRequestType $transactionRequestType
     * @return array
     */
    private function createTransactionRequest(TransactionRequestType $transactionRequestType): array
    {
        $refId = $this->getRefId();
        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($this->client);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($this->getEnvironment());

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    return [
                        'status' => true,
                        'refId' => $refId,
                        'transaction_id' => $tresponse->getTransId(),
                        'response_code' => $tresponse->getResponseCode(),
                        'message_code' => $tresponse->getMessages()[0]->getCode(),
                        'auth_code' => $tresponse->getAuthCode(),
                        'description' => $tresponse->getMessages()[0]->getDescription(),
                    ];

                } else {
                    return [
                        'status' => false,
                        'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                        'error_message' => $tresponse->getErrors()[0]->getErrorText(),
                    ];
                }
            } else {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return [
                        'status' => false,
                        'error_code' => $tresponse->getErrors()[0]->getErrorCode(),
                        'error_message' => $tresponse->getErrors()[0]->getErrorText(),
                    ];
                } else {
                    return [
                        'status' => false,
                        'error_code' => $response->getMessages()->getMessage()[0]->getCode(),
                        'error_message' => $response->getMessages()->getMessage()[0]->getText(),
                    ];
                }
            }
        } else {
            return [
                'status' => false,
                'error_code' => null,
                'error_message' => __('errors.not_answer'),
            ];
        }
    }

    /**
     * @param int $cycles
     * @param string $unit
     * @return array
     */
    private function prepareRecurringData(int $cycles, string $unit): array
    {
        $length = 0;

        switch ($unit) {
            case RecurringPeriod::DAY:
            case RecurringPeriod::WEEK:
                $unit = AuthorizeCurringPeriod::DAY;
                $length = 7;
                break;
            case RecurringPeriod::MONTH:
                $unit = AuthorizeCurringPeriod::MONTH;
                $length = 1;
                break;
            case RecurringPeriod::YEAR:
                $unit = AuthorizeCurringPeriod::MONTH;
                $length = 12;
        }

        $cycles = $cycles === 0 ? self::WITHOUT_END_DATE : $cycles - self::MINUS_CYCLES;

        return [
            'length' => $length,
            'unit' => $unit,
            'cycles' => $cycles,
        ];
    }

    /**
     * @param array $payload
     * @return Orders
     */
    private function prepareOrderForSubscription(array $payload): Orders
    {
        $order = $payload['order'];

        $order->products = [
            [
                'id' => $payload['product_id'],
                'name' => $payload['name'],
                'quantity' => 1,
                'discount_price' => $payload['total'],
                'tax' => 0,
            ],
        ];
        $order->note = '';
        $order->tax = 0;

        return $order;
    }

    /**
     * @return string
     */
    private function getRefId(): string
    {
        return 'ref' . time();
    }

    /**
     * @param $transactionID
     * @return CustomerProfileIdType
     * @throws Exception
     */
    private function createCustomerProfile($transactionID): CustomerProfileIdType
    {
        $request = new CreateCustomerProfileFromTransactionRequest();
        $request->setMerchantAuthentication($this->client);
        $request->setTransId($transactionID);

        $controller = new CreateCustomerProfileFromTransactionController($request);

        $response = $controller->executeWithApiResponse($this->getEnvironment());

        if (!(($response != null) && ($response->getMessages()->getResultCode() == "Ok"))) {
            $errorMessages = $response->getMessages()->getMessage();

            throw new Exception($errorMessages[0]->getText());
        }

        $customerProfileData = [
            'customer_profile_id' => $response->getCustomerProfileId(),
            'customer_payment_profile_id' => $response->getCustomerPaymentProfileIdList()[0],
        ];

        $profile = new CustomerProfileIdType();
        $profile->setCustomerProfileId($customerProfileData['customer_profile_id']);
        $profile->setCustomerPaymentProfileId($customerProfileData['customer_payment_profile_id']);

        //SANDBOX need to wait for the customer to be created otherwise E00040
        if ($this->getEnvironment() === ANetEnvironment::SANDBOX) {
            sleep(10);
        }

        return $profile;
    }

    /**
     * @return string
     */
    private function getEnvironment(): string
    {
        switch ($this->paymentMethod->settings['mode']) {
            case AuthorizeMethod::MODE_PRODUCTION:
                return ANetEnvironment::PRODUCTION;
            default:
                return ANetEnvironment::SANDBOX;
        }
    }

    /**
     * @param $transactionID
     * @return AnetApiResponseType
     * @throws Exception
     */
    public function getTransaction($transactionID): AnetApiResponseType
    {
        $request = new GetTransactionDetailsRequest();
        $request->setMerchantAuthentication($this->client);
        $request->setRefId($this->getRefId());

        $request->setTransId($transactionID);

        $controller = new GetTransactionDetailsController($request);

        $response = $controller->executeWithApiResponse($this->getEnvironment());

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return $response;
        } else {
            throw new Exception($response->getMessages()->getMessage());
        }
    }

    /**
     * @param int $cycles
     * @param string $recurringPeriod
     * @return PaymentScheduleType
     */
    private function createPaymentSchedule(int $cycles, string $recurringPeriod): PaymentScheduleType
    {
        $recurringData = $this->prepareRecurringData($cycles, $recurringPeriod);

        $interval = new IntervalAType();
        $interval->setLength($recurringData['length']);

        $interval->setUnit($recurringData['unit']);

        $paymentSchedule = new PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate($this->getStartSubscribeData($recurringData['unit'], $recurringData['length']));
        $paymentSchedule->setTotalOccurrences($recurringData['cycles']);

        return $paymentSchedule;
    }

    /**
     * @param $orderID
     * @param string $description
     * @return OrderType
     */
    private function crateOrder($orderID, string $description): OrderType
    {
        $order = new OrderType();
        $order->setInvoiceNumber($orderID);
        $order->setDescription($description);

        return $order;
    }

    /**
     * @param ARBSubscriptionType $subscription
     * @return array
     */
    private function createSubscriptionRequest(ARBSubscriptionType $subscription): array
    {
        // Set the transaction's refId
        $refId = $this->getRefId();

        $request = new ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($this->client);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new ARBCreateSubscriptionController($request);

        $response = $controller->executeWithApiResponse($this->getEnvironment());

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return [
                'subscription_id' => $response->getSubscriptionId(),
                'status' => true,
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            return [
                'error_code' => $errorMessages[0]->getCode(),
                'error_message' => $errorMessages[0]->getText(),
                'status' => false,
            ];
        }
    }

    /**
     * @param string $unit
     * @param int $length
     * @return DateTime
     */
    private function getStartSubscribeData(string $unit, int $length): DateTime
    {
        switch ($unit) {
            case AuthorizeCurringPeriod::DAY:
                return Carbon::now()->addDays($length)->toDate();
            case AuthorizeCurringPeriod::MONTH:
                return Carbon::now()->addMonths($length)->toDate();
        }
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return MerchantAuthenticationType
     */
    private function getClient(PaymentMethod $paymentMethod): MerchantAuthenticationType
    {
        $client = new MerchantAuthenticationType();
        $client->setName($paymentMethod->settings['login_id']);
        $client->setTransactionKey($paymentMethod->settings['transaction_key']);

        return $client;
    }

    public function refund(Orders $order, Refund $refund)
    {
        try {
            $transaction = $this->getTransaction($order->external_id);


            $creditCard = new CreditCardType();
            $creditCard->setCardNumber(substr($transaction->getTransaction()->getPayment()->getCreditCard()->getCardNumber(),
                -4));
            $creditCard->setExpirationDate($transaction->getTransaction()->getPayment()->getCreditCard()->getExpirationDate());
            $paymentOne = new PaymentType();
            $paymentOne->setCreditCard($creditCard);
            //create a transaction

            $transactionRequest = new TransactionRequestType();
            $transactionRequest->setTransactionType("refundTransaction");
            $transactionRequest->setAmount($refund->amount);
            $transactionRequest->setPayment($paymentOne);
            $transactionRequest->setRefTransId($order->external_id);

            $request = new CreateTransactionRequest();
            $request->setmerchantAuthentication($this->client);
            $request->setRefId($this->getRefId());
            $request->setTransactionRequest($transactionRequest);
            $controller = new CreateTransactionController($request);
            $response = $controller->executeWithApiResponse($this->getEnvironment());

            $tresponse = $response->getTransactionResponse();

            if ($response != null) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $order->update([
                            'payment_status' => $order->amount > $order->refunded() ? PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL : PaymentStatus::PAYMENT_STATUS_REFUND,
                        ]);

                        $refund->update([
                            'data' => $tresponse->jsonSerialize(),
                        ]);

                        return true;

                    } else {
                        throw new Exception($tresponse->getErrors()[0]->getErrorText() . "\n" . $tresponse->getErrors()[0]->getErrorCode());
                    }
                } else {
                    throw new Exception($tresponse->getErrors()[0]->getErrorText() . "\n" . $tresponse->getErrors()[0]->getErrorCode());
                }
            } else {
                return false;
            }

        } catch (Exception $e) {
            $refund->delete();
            throw $e;
        }
    }

    /**
     * @param string $subscriptionID
     * @return AnetApiResponseType|null
     */
    private function getSubscription(string $subscriptionID): ?AnetApiResponseType
    {
        $request = new ARBGetSubscriptionStatusRequest();
        $request->setMerchantAuthentication($this->client);
        $request->setRefId($this->getRefId());
        $request->setSubscriptionId($subscriptionID);

        $controller = new ARBGetSubscriptionStatusController($request);

        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return $response;
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            $message = "Response : " . $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText() . "\n";
            Log::error($message);

            return null;
        }
    }

    /**
     * @param Subscription $subscription
     * @return bool
     * @throws \ReflectionException
     */
    public function checkSubscription(Subscription $subscription): bool
    {
        $subscriptionData = $this->getSubscription($subscription->orders()->first()->external_id);

        switch ($subscriptionData->getStatus()) {
            case AuthorizeSubscriptionStatus::ACTIVE:
                return true;
            case AuthorizeSubscriptionStatus::CANCELED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::CANCELED);
                break;
            case AuthorizeSubscriptionStatus::SUSPENDED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::SUSPENDED);
                break;
            case AuthorizeSubscriptionStatus::EXPIRED:
            case AuthorizeSubscriptionStatus::TERMINATED:
                $this->subscriptionService->changeStatus($subscription, SubscriptionStatus::EXPIRED);
                break;
        }
    }
}
