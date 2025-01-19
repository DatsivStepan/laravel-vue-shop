<template>
    <div class="checkout-page row">
        <div class="col-12">
            <div class="content-header clearfix">
                <h2>
                    {{ translate('orders.orders') }}
                </h2>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row" v-if="order.id">
                <div class="col-lg-8  order-0 order-lg-1">
                    <div class="content-folder">
                        <div class="tabs-content">
                            <component :is="'checkout-edit-products'" :order="order"
                                       :tabs="tabs" @get-order-data="getOrderData"></component>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 order-lg-2 mb-9 mb-lg-0 order-1 order-lg-0">
                    <div class="content-folder-checkout">
                        <refund-pay-block :order="order"></refund-pay-block>
                    </div>
                </div>
            </div>
        </div>

        <div class="card rounded-0 empty-cart-folder" v-if="!order.id">
            <div class="card-body">
                <div class="row justify-content-center empty-cart">
                    <div class="ckeckout-card">
                        <div class="text-center">
                            <div class="bg-secondary h2 cart-default-icon">
                                <span class="icon-empty-cart"></span>
                            </div>
                        </div>
                        <h3 class="text-center title">{{ translate('orders.yourcartisempty') }}</h3>
                        <p class="text-center text">
                            {{ translate('orders.youmustaddproduct') }}
                        </p>
                        <div class="text-center">
                            <router-link class="btn" :to="{ name : 'catalog' }">
                                {{ translate('orders.startshopping') }}
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import '@sass/checkout.scss';
import {CheckoutEditProducts, RefundPayBlock } from '@component/orders';

export default {
    components: {
        CheckoutEditProducts,
        RefundPayBlock,
    },
    props: {
        order_id: {
            type: Number,
            required: true
        }
    },
    data: function () {
        return {
            enableShipping: false,
            order: {
                id: null,
                products: [],

            },
            tabs: null,
        }
    },
    mounted() {
        let app = this;
        app.getOrderData();
    },

    methods: {

        getOrderData() {
            let app = this;

            let params = {
                order_id: this.order_id

            }

            axios.get('/orders/get-order-data', {params: params})
                .then(function (resp) {
                    app.order = resp.data;
                    app.order.billingType = resp.data.billing_type;
                    app.enableShipping = resp.data.enable_shipping;
                    app.order.invoice = true
                })
                .catch(function () {
                    console.error("error get orders");
                });
        },
    }
}
</script>
