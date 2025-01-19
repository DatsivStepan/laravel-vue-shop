<template>

    <div v-if="order">
        <template v-if="!order.isSub">
            <div class="media-li" v-for="(product, key) in order.products">
                <div class="d-flex position-relative">
                    <div class="u-lg-avatar" :style="{backgroundImage:'url(' + product.image_src + ')'}"></div>
                </div>
                <div class="media-body" v-if="!order.isSub">
                    <h4 class="title-product">{{ product.name }}</h4>
                    <span class="d-block  text-info-product">
                    {{ translate('orders.price') }}:
                    {{ product.formatted_price }}</span>
                    <span class="d-block  text-info-product"
                          v-if="getSettingValue('enable_discounts') == 1 && order.discount">
                    {{ translate('orders.discount_price') }}:{{ product.formatted_discount_price }}</span>
                    <span class="d-block  text-info-product">{{ translate('orders.quantity') }}:
                    {{ product.quantity }}</span>
                    <button class="delete-product" @click="removeFromCart(product.id, product.quantity)">
                        <ion-icon name="close"></ion-icon>
                    </button>
                </div>
            </div>
        </template>
        <div class="media-li" v-else>
            <div class="d-flex position-relative">
                <div class="u-lg-avatar" :style="{backgroundImage:'url(' + order.image_src + ')'}"></div>
            </div>
            <div class="media-body">
                <h4 class="title-product">{{ order.name }}</h4>
                <span class="text-info-product">{{ translate('orders.price') }}:
                    {{ order.formatted_price }}</span>
                <span class="text-info-product" v-if="getSettingValue('enable_taxes') == 1">{{
                        order.tax.label
                    }}: {{ order.tax.value }}</span>
                <span class="text-info-product">{{ translate('orders.total') }}:
                    {{ order.formatted_total }}</span>
                <span class="text-info-product">{{ translate('subscriptions.recurring_period') }}:
                    {{ order.recurring_period }}</span>
                <span class="text-info-product">{{ translate('subscriptions.billing_cycles') }}:
                    {{ order.billing_cycles }}</span>
                <button class="delete-product" @click="redirectToCatalog()">
                    <ion-icon name="close"></ion-icon>
                </button>
            </div>

        </div>
        <div class="summary-folder" v-if="billingType === 'subscription'">
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ translate('orders.subtotal') }}</span>
                <span class="summary-folder-number">{{ order.formatted_price }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li"
                 v-if="getSettingValue('enable_taxes') == 1">
                <span class="summary-folder-label">{{ order.tax.label }}</span>
                <span class="summary-folder-number">{{ order.tax.value }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ translate('subscriptions.recurring_period') }}</span>
                <span class="summary-folder-number">{{ order.recurring_period }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ translate('subscriptions.billing_cycles') }}</span>
                <span class="summary-folder-number">{{ order.billing_cycles }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between total-list">
                <span class="summary-folder-label">{{ translate('orders.total') }}</span>
                <span class="summary-folder-number">{{ order.formatted_total }}</span>
            </div>
        </div>
        <!-- FOR SUBSCRIPTION PRODUCTS -->
        <div class="summary-folder" v-else>
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ translate('orders.subtotal') }}</span>
                <span class="summary-folder-number">{{ order.formatted_subtotal }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li"
                 v-if="getSettingValue('enable_discounts') == 1 && order.discount">
                <span class="summary-folder-label">{{ translate('discounts.discount') }}</span>
                <span class="summary-folder-number">{{ order.formatted_discount }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li"
                 v-if="getSettingValue('enable_taxes') == 1">
                <span class="summary-folder-label">{{ order.tax.label }}</span>
                <span class="summary-folder-number">{{ order.tax.value }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between total-list">
                <span class="summary-folder-label">{{ translate('orders.total') }}</span>
                <span class="summary-folder-number">{{ order.formatted_total }}</span>
            </div>
        </div>
        <div v-if='tabs' class="buttons-checkout-options">
            <checkout-pagination v-bind:tabs="tabs" v-bind:in="'products'" v-on:plus-tab="plusTab"
                                 v-on:minus-tab="minusTab" ref="pagination"></checkout-pagination>
        </div>
    </div>

</template>

<script>
import CheckoutPagination from './Pagination.vue';

export default {
    components: {
        CheckoutPagination
    },
    props: ['order', 'tabs', 'billingType'],
    data: function () {
        return {}
    },
    methods: {
        plusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        minusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        redirectToCatalog() {
            this.$router.push({name: 'catalog'});
        },
        removeFromCart(productId, quantity) {
            let app = this;
            let confirmOption = {
                cancelButtonText: app.translate('system.confirm.no'),
                confirmButtonText: app.translate('system.confirm.yes'),
            }
            this.$confirm(app.translate('system.confirm.question.you_want_delete'), app.translate('system.delete'), '', confirmOption).then(() => {
                let params = {
                    order_id: app.order.id,
                    product_id: productId,
                    quantity: quantity
                }
                axios.delete('/orders/remove-from-cart', {data: params}).then(function (resp) {
                    app.$emit('get-order-data')
                    app.$store.commit('removeProductFromCart', 1);
                })
                .catch(function (resp) {
                    console.log('error delete categories');
                });
            });
        },
    },
}
</script>
