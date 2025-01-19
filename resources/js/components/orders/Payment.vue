<template>
    <div class="payment-data-page">
        <b-alert
            v-model="showSelectPaymentMethodMessage"
            variant="danger"
            dismissible
        >
            {{ translate('errors.orders.checkout.please_select_payment_method') }}
        </b-alert>
        <div class="payment-data" v-for="(item, key) in payments" :key="key">
            <label class="d-flex align-items-center justify-content-between">
                <span class="payment-data-name">{{ item.name }}</span>
                <div class="d-flex align-items-center justify-content-end">
                    <img
                        v-if="item.icon"
                        :src="item.icon"
                        alt="image"
                        class="payment-data-img"
                    >
                    <input v-model="activePaymentMethod" :value="item" type="radio" name="payment-method"
                           class="payment-data-method">
                    <span class="payment-data-status"></span>
                </div>
            </label>
        </div>
        <div class="options-data payment">
            <div class="options-data-list">
                <div class="d-flex align-items-center justify-content-between options-data-total">
                    <span class="options-data-total-label">{{ translate('orders.total') }}</span>
                    <span class="options-data-total-number">{{ order.formatted_total }}</span>
                </div>
            </div>
        </div>
        <div v-if='tabs' class="buttons-checkout-options">
            <checkout-pagination :tabs="tabs" :in="'payment'" @plus-tab="plusTab"
                                 v-on:minus-tab="minusTab" ref="pagination">
                <template v-slot:payment-button>
                    <payment-invoice
                        v-if="activePaymentMethod"
                        ref="paymentInvoice"
                        :payment="activePaymentMethod"
                        :order="order"
                        :billing-type="billingType"
                        @order-completed="orderCompleted"
                    >
                    </payment-invoice>
                    <button v-else type="button" class="btn btn-success" @click="showSelectPaymentMethodMessage=true">
                        {{ translate('orders.processpayment') }}
                    </button>
                </template>
            </checkout-pagination>

        </div>

    </div>

</template>
<script>
import CheckoutPagination from './Pagination.vue';
import PaymentInvoice from './PaymentInvoice.vue';
import PaymentPayPal from '@modules/Payments/Resources/js/components/PaymentPayPal.vue';

export default {
    components: {
        CheckoutPagination,
        PaymentInvoice,
        PaymentPayPal
    },
    props: {
        order: Object,
        tabs: Array,
        billingType: {
            type: String,
            default: 'regular'
        }
    },
    data: function () {
        return {
            payments: {},
            pTabsIndex: 0,
            activePaymentMethod: null,
            showSelectPaymentMethodMessage: false,
            paymentMethod: [
                {name: 'Stripe', imgName: '1'},
                {name: 'PayPal', imgName: '2'},
                {name: 'Authorize.net', imgName: '3'},
                {name: 'CyberSourse', imgName: '4'},
                {name: 'Touchnet', imgName: '5'},
                {name: 'QuickBoos-qw', imgName: '6'},
                {name: 'QuickBoosDesktop', imgName: '7'},
                {name: 'QuickBoosDesktop', imgName: '8'},
                {name: 'Stripe SCA', imgName: '9'},
            ]
        }
    },
    mounted() {
        let app = this;
        app.getPaymentMethods();
    },
    watch: {
        order() {
            this.getPaymentMethods();
        }
    },
    methods: {
        orderCompleted(orderID) {
            let app = this;

            app.$store.commit('removeProductFromCart', app.$store.state.productInCart);
            app.$store.commit('removeProductFromFavorite', app.$store.state.productInFavorite);
            app.$router.push({
                name: 'checkout-complete',
                params: {order_id: orderID, is_invoice: this.activePaymentMethod.method === 'invoice'}
            });
        },
        plusTab(toId) {
        },
        minusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        getPaymentMethods() {
            let app = this;

            axios.get('/orders/payment-methods')
                .then(function (resp) {
                    app.payments = resp.data.payments;
                })
                .catch(function () {
                    console.log("error get orders");
                });
        }
    }
}
</script>
