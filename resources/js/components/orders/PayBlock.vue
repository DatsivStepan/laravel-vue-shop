<template>
    <div v-if="order">
        <div>
            <h2 class='title'>{{translate('orders.ordersummary')}}</h2>

                <div v-if="!order.isSub && getSettingValue('enable_coupons') == 1">
                    <div class="voucher">
                        <span class="question">{{ translate('orders.have_coupon') }}</span>
                        <span class="info">{{ translate('orders.apply_coupon') }}</span>
                    </div>
                    <div class="text-right apply-coupon">
                        <input placeholder="Add coupon" v-model="couponCode"/>
                        <button class="btn btn-primary btn-apply" @click.prevent="addCoupon()">
                            {{ translate('system.apply') }}
                        </button>
                        <div class="coupons-folder text-left">
                            <h3 v-if="isShowCoupons" class="title-coupon">{{ translate('system.coupons') }}</h3>
                            <div v-for="coupon in order.coupons"
                                 class="d-flex align-items-center justify-content-between coupon-list">
                                <span class="coupon-list-item">{{ coupon.code }}</span>
                                <span @click="deleteCoupon(coupon.id)" class="coupon-list-close">
                                <ion-icon name="close"></ion-icon>
                                </span>
                            </div>

                            <div v-for="product_seat in order.product_seats"
                                class="d-flex align-items-center justify-content-between coupon-list">
                                <span class="coupon-list-item">{{ product_seat.seat_key }}</span>
                                <span @click="deleteSeat(product_seat.id)" class="coupon-list-close">
                                <ion-icon name="close"></ion-icon>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!order.isSub">
                    <div class="discount"
                         v-if="order.billingType === 'regular' && getSettingValue('enable_discounts') == 1 && order.discounts.length">
                        <h5 class="discount-title"><b>{{ translate('system.discounts') }}</b></h5>
                        <span class="discount-number" v-for="(discount, key) in order.discounts" :key="key">{{
                                discount
                            }}</span>
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
                    <div class="d-flex align-content-center justify-content-between"
                         v-if="getSettingValue('enable_taxes') == 1">
                        <h5 class="info-order-title">{{ order.tax.label }}</h5>
                        <span class="info-order-number">{{ order.tax.value }}</span>
                    </div>
                </div>
                <div class="info-order" v-else>
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="info-order-title">{{ translate('orders.subtotal') }}</h5>
                        <span class="info-order-number">{{ order.formatted_subtotal }}</span>
                    </div>
                    <div class="d-flex align-content-center justify-content-between"
                         v-if="getSettingValue('enable_taxes') == 1">
                        <h5 class="info-order-title">{{ order.tax.label }}</h5>
                        <span class="info-order-number">{{ order.tax.value }}</span>
                    </div>
                    <div class="d-flex align-content-center justify-content-between"
                         v-if="getSettingValue('enable_discounts') == 1 && order.discount">
                        <h5 class="info-order-title">{{ translate('discounts.discount') }}</h5>
                        <span class="info-order-number">{{ order.formatted_discount }}</span>
                    </div>
                </div>
            </div>
        </div>
            <div v-if="order.isSub">
                <div class="info-order">
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="info-order-title">{{ translate('orders.price') }}</h5>
                        <span class="info-order-number">{{ order.formatted_price }}</span>
                    </div>
                    <div class="d-flex align-content-center justify-content-between"
                         v-if="getSettingValue('enable_taxes') == 1">
                        <h5 class="info-order-title">{{ order.tax.label }}</h5>
                        <span class="info-order-number">{{ order.tax.value }}</span>
                    </div>
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="info-order-title">{{ translate('subscriptions.recurring_period') }}</h5>
                        <span class="info-order-number">{{ order.recurring_period }}</span>
                    </div>
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="info-order-title">{{ translate('subscriptions.billing_cycles') }}</h5>
                            <span class="info-order-number">{{ order.billing_cycles }}</span>
                    </div>
                </div>
            </div>
                <div class="total-order">
                    <div class="d-flex align-content-center justify-content-between">
                        <h5 class="total-order-title">{{ translate('orders.total') }}</h5>
                        <span class="total-order-number">{{ order.formatted_total }}</span>
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
        product: {
            type: Object
        }
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
        }
    }
}
</script>
