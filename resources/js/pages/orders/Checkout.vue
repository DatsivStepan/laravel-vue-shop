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
                <div class="col-lg-4 order-lg-2 mb-9 mb-lg-0 order-1 order-lg-0">
                    <div class="content-folder-checkout">

                        <checkout-pay-block :order="order" :products="order.products"
                                            @get-order-data="getOrderData"></checkout-pay-block>
                    </div>
                </div>

                <div class="col-lg-8  order-0 order-lg-1">
                    <div class="content-folder">
                        <checkout-menu :tabs="tabs"></checkout-menu>
                        <div class="select-currency">
                            <user-currency v-if="order.id" @changeCurrency="changeCurrency"></user-currency>
                        </div>
                        <div class="tabs-content">
                            <component :is="'checkout-' + $store.state.cartTabActive" :order="order"
                                       :tabs="tabs" @get-order-data="getOrderData"></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card rounded-0 empty-cart-folder" v-if="!order.id">
            <div class="card-body">
                <div  class="row justify-content-center empty-cart">
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
import {
    CheckoutCustomerInfo,
    CheckoutMenu,
    CheckoutOptions,
    CheckoutPayBlock,
    CheckoutPayment,
    CheckoutProducts,
    CheckoutShipping,
} from '@component/orders';
import UserCurrency from "../../components/user/UserCurrency";

export default {
    components: {
        UserCurrency,
        CheckoutMenu,
        CheckoutProducts,
        CheckoutPayBlock,
        CheckoutCustomerInfo,
        CheckoutShipping,
        CheckoutPayment,
        CheckoutOptions,
    },
    data: function () {
        return {
            enableShipping: false,
            order: {
                id: null,
                products: []
            },
            tabs: [],
        }
    },
    mounted() {
        let app = this;
        app.getOrderData();
    },
    methods: {
        getOrderData(billingType) {
            let app = this;

            let params = {
                billing_type: billingType
            }

            axios.get('/orders/get-order-data', {params: params})
                .then(function (resp) {
                    app.order = resp.data;
                    app.order.billingType = resp.data.billing_type;
                    app.enableShipping = resp.data.enable_shipping;
                    app.createTabs();
                })
                .catch(function () {
                    console.error("error get orders");
                });
        },
        createTabs() {
            let app = this;
            app.tabs = [];
            app.tabs.push({
                title: app.translate('orders.tab_product_title'),
                contentKey: 'products'
            });

            if (app.getSettingValue('enable_customer_info') == 1) {
                app.tabs.push({
                    title: app.translate('orders.tabcustomertitle'),
                    contentKey: 'customer-info'
                });
            }

            if ((app.getSettingValue('enable_shipping') == 1) && app.enableShipping) {
                app.tabs.push({
                    title: app.translate('orders.tabshippingtitle'),
                    contentKey: 'shipping'
                });
            }

            if (app.getSettingValue('enable_installment') == 1) {
                app.tabs.push({
                    title: app.translate('orders.tab_options_title'),
                    contentKey: 'options'
                });
            }

            app.tabs.push({
                title: app.translate('orders.tabpaymenttitle'),
                contentKey: 'payment'
            });

            if (app.$store.state.cartTabActive == '') {
                app.$store.commit('setActiveCartTab', app.tabs[0].contentKey);
            }
        },
        changeCurrency() {
            this.getOrderData()
        },
    }
}
</script>
