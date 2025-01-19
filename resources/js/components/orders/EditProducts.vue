<template>

    <div>
        <div class="media-li" v-for="(product, key) in order.products">
            <div class="d-flex position-relative">
                <div class="u-lg-avatar" :style="{backgroundImage:'url(' + product.image_src + ')'}"></div>
            </div>
            <div class="media-body">
                <h4 class="title-product">{{ product.name }}</h4>
                <span class="d-block  text-info-product">{{ translate('orders.price') }}:
                    {{ product.formatted_price }}</span>
                <span class="d-block  text-info-product">{{ translate('orders.discount_price') }}:
                    {{ product.formatted_discount_price }}</span>
                <span class="d-block  text-info-product">{{ translate('orders.quantity') }}:
                    {{ product.quantity }}</span>
            </div>
            <input class="table-input" type="checkbox" :id="key" @click="select(key)">
                <label :for="key"></label>
        </div>
        <div class="summary-folder">
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ translate('orders.subtotal') }}</span>
                <span class="summary-folder-number">{{ order.formatted_subtotal }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ translate('discounts.discount') }}</span>
                <span class="summary-folder-number">{{ order.formatted_discount }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between list-li">
                <span class="summary-folder-label">{{ order.tax.label }}</span>
                <span class="summary-folder-number">{{ order.tax.value }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between total-list">
                <span class="summary-folder-label">{{ translate('orders.total') }}</span>
                <span class="summary-folder-number">{{ order.formatted_total }}</span>
            </div>
        </div>
        <div class="buttons-checkout-options refund-action">
            <div class="d-sm-flex justify-content-sm-end align-items-sm-center">

                <button type="button" class="btn btn-success refund-btn" @click="remove">
                    {{ translate('orders.remove') }}
                </button>
            </div>
        </div>
        <!-- <button class="delete-product" @click="remove(product.id, product.quantity)">
                <ion-icon name="close"></ion-icon>
            </button> -->
    </div>

</template>

<script>
import CheckoutPagination from './Pagination.vue';

export default {
    components: {
        CheckoutPagination
    },
    props: ['order',],
    data: function () {
        return {

            product: {
                id: []
            },
            selectProducts: []

        }
    },
    methods: {
        select(index) {
            if (this.selectProducts.includes(index)) {
                this.selectProducts.splice(this.selectProducts.indexOf(index), 1);
            } else {
                this.selectProducts.push(index)
            }
        },
        plusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        minusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        remove() {
            let app = this;
            let confirmOption = {
                cancelButtonText: app.translate('system.confirm.no'),
                confirmButtonText: app.translate('system.confirm.yes'),
            }
            this.$confirm(app.translate('system.confirm.question.you_want_delete'), app.translate('system.delete'), '', confirmOption).then(() => {

                let products = app.selectProducts.map((index) => {
                    let product = app.order.products[index]
                    return {
                        id: product.id,
                        quantity: product.quantity
                    }
                })

                let params = {
                    order_id: app.order.id,
                    products: products,
                }

                axios.delete('/orders/remove-from-cart', {data: params}).then(function (resp) {

                    if ((app.order.product.length - products.length) > 0) {
                        app.$emit('get-order-data')
                    } else {
                        app.$router.push({name: 'admin-invoices'});
                    }
                })

            });
        },
    }
}
</script>
