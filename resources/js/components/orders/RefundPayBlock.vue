<template>
    <div v-if="order">
        <div class="refund-summary">
            <h2 class='title'>{{translate('orders.ordersummary')}}</h2>
            <div v-if="!order.invoice">
                <div class="discount" v-if="order.billingType === 'regular'">
                    <h5 class="discount-title"><b>{{ translate('system.discounts') }}</b></h5>
                    <span class="discount-number" v-for="(discount, key) in order.discounts" :key="key">{{
                            discount
                        }}</span>
                </div>
            </div>
            <div class="info-order" v-if="order.billing_type === 'installment'">
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('orders.installments.recurring_period') }}</h5>
                    <span class="info-order-number">{{ order.recurring_period }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('orders.installments.billing_cycles') }}</h5>
                    <span class="info-order-number">{{ order.billing_cycles }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('orders.installments.price') }}</h5>
                    <span class="info-order-number">{{ order.formatted_installment_price }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('orders.installments.fee') }}</h5>
                    <span class="info-order-number">{{ order.formatted_fee }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ order.tax.label }}</h5>
                    <span class="info-order-number">{{ order.tax.value }}</span>
                </div>
            </div>
            <div class="info-order" v-else>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('orders.subtotal') }}</h5>
                    <span class="info-order-number">{{ order.formatted_subtotal }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ order.tax.label }}</h5>
                    <span class="info-order-number">{{ order.tax.value }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('discounts.discount') }}</h5>
                    <span class="info-order-number">{{ order.formatted_discount }}</span>
                </div>
            </div>

             <div class="total-order">
                 <div class="d-flex align-content-center justify-content-between">
                     <h5 class="total-order-title">{{ translate('orders.total') }}</h5>
                     <span class="total-order-number">{{ order.formatted_total }}</span>
                 </div>
                <div v-if="!order.invoice">
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="total-order-title">{{ translate('orders.refunds.refunded') }}</h5>
                        <span class="total-order-number">{{ order.formatted_total }}</span>
                    </div>
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="total-order-title">{{ translate('orders.refunds.maximum_amount') }}</h5>
                        <span class="total-order-number">{{ order.formatted_total }}</span>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../api";
import '@sass/component/product-btns.scss'
export default {
    props: {
        order: {
            type: Object
        },
    },
    data: function () {
        return {
            couponCode: '',
        };
    },
    computed: {
        isShowCoupons() {
            return this.order.coupons && this.order.coupons.length ||
                this.order.product_seats && this.order.product_seats.length;
        }
    },
    methods: {
        deleteCoupon(couponId) {
            let app = this;
            api.deleteRequest(api.urls.order.deleteCoupon, {order_id: this.order.id, coupon_id: couponId})
                .then(function (resp) {
                    app.$emit('get-order-data')
                })
        },
        deleteSeat(seatUseId) {
            let app = this;
            api.deleteRequest(api.urls.order.deleteSeatUse, {order_id: this.order.id, seat_use_id: seatUseId})
                .then(function (resp) {
                    app.$emit('get-order-data')
                })
        },
        addCoupon() {
            let app = this;
            api.post(api.urls.order.addCoupon, {order_id: this.order.id, coupon_code: this.couponCode})
                .then(function (resp) {
                    app.$emit('get-order-data')
                    app.couponCode = '';
                })
        },
    }
}
</script>