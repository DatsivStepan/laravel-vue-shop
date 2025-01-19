<template>
    <div>
        <div>
            <h2 class="title">{{ translate('orders.ordersummary') }}</h2>
                <div class="product-list">
                    <div class="media-li">
                        <div class="d-flex position-relative">
                            <div class="u-lg-avatar"  :style="{backgroundImage:'url(' + product.image_src + ')'}">
                            </div>
                        </div>
                        <div class="media-body">
                            <h4 class="title-product">{{ product.name }}</h4>
                            <span class="text-info-product">{{ translate('orders.price') }}:
                            {{ product.formatted_price }}</span>
                            <span class="text-info-product">{{ product.tax.label }}: {{ product.tax.value }}</span>
                            <span class="text-info-product">{{ translate('orders.total') }}:
                            {{ product.formatted_total }}</span>
                            <span class="text-info-product">{{ translate('subscriptions.recurring_period') }}:
                            {{ product.recurring_period }}</span>
                            <span class="text-info-product">{{ translate('subscriptions.billing_cycles') }}:
                            {{ product.billing_cycles }}</span>
                        </div>
                        <button class="delete-product" @click="redirectToCatalog()">
                            <ion-icon name="close"></ion-icon>
                        </button>
                    </div>
                </div>
            <div class="info-order">
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('orders.price') }}</h5>
                    <span class="info-order-number">{{ product.formatted_price }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ product.tax.label }}</h5>
                    <span class="info-order-number">{{ product.tax.value }}</span>
                </div>
            </div>
            <div class="info-order">
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('subscriptions.recurring_period') }}</h5>
                    <span class="info-order-number">{{ product.recurring_period }}</span>
                </div>
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="info-order-title">{{ translate('subscriptions.billing_cycles') }}</h5>
                    <span class="info-order-number">{{ product.billing_cycles }}</span>
                </div>
            </div>
            <div class="total-order">
                <div class="d-flex align-content-center justify-content-between">
                    <h5 class="total-order-title">{{ translate('orders.total') }}</h5>
                    <span class="total-order-number">{{ product.formatted_total }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        product: {
            type: Object,
            required: true
        }
    },
    data: function () {
        return {
            couponCode: '',
        };
    },
    methods: {
        redirectToCatalog() {
            this.$router.push({name: 'catalog'});
        }
    }
}
</script>
