<template>
    <div class="checkout-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 order-lg-2 mb-9 mb-lg-0">
                    <div class="content-folder-checkout">
                        <!-- <subscribe :product="product"/> -->
                        <checkout-pay-block :order="subs" ></checkout-pay-block>
                    </div>
                </div>
                <div class="col-lg-8 order-lg-1" v-if="order.product_id">
                    <div class="content-folder">
                        <checkout-menu :tabs="tabs"></checkout-menu>
                        <div class="select-currency">
                            <user-currency v-if="order.product_id" @changeCurrency="changeCurrencySub"></user-currency>
                        </div>
                        <div class="tabs-content">
                            <component :is="'checkout-' + $store.state.cartTabActive" :order="order"
                                       :tabs="tabs" billingType="subscription"></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
import {
    CheckoutCustomerInfo,
    CheckoutMenu,
    CheckoutPayBlock,
    CheckoutPayment,
    CheckoutProducts,
    CheckoutShipping,
} from '@component/orders';
import api from "@/api";
import Subscribe from "@component/orders/Subscribe";
import UserCurrency from "@component/user/UserCurrency";


export default {
    components: {
        Subscribe, CheckoutMenu, CheckoutProducts,
        CheckoutCustomerInfo,
        CheckoutShipping,
        CheckoutPayment,
        UserCurrency,
        CheckoutPayBlock,

    },
    props: {
        product_id: {
            type: Number,
            required: true
        }
    },
    data: function () {
        return {
            product: {
                id: null,
                billing_cycles: 0,
                currency: null,
                formatted_price: '',
                formatted_total: '',
                image_src: '',
                name: '',
                price: 0,
                recurring_period: '',
                tax: {
                    label: '',
                    value: 0,
                },
                total: 0,
                isSub: false

            },
            tabs: [],
        }
    },
    computed: {
        order() {
            let order = this.product;
            order.product_id = this.product.id
            order.discount = 0;
            order.subtotal = this.product.price
            order.product_id = this.product.id
            return order;
        },
        subs(){
            let order = this.product;
            order.isSub = !order.isSub
            return order;
        }
    },
    created() {
        this.$store.state.cartTabActive = '';
        this.getSubscribeData();
    },
    methods: {
        getSubscribeData() {
            let params = {
                product_id: this.product_id
            }
            let app = this;
            api.get(api.urls.subscribe.getProduct, params).then(res => {
                app.product = res.data.data;
                app.createTabs();
            })
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

            app.tabs.push({
                title: app.translate('orders.tabpaymenttitle'),
                contentKey: 'payment'
            });

            if (app.$store.state.cartTabActive == '') {
                app.$store.commit('setActiveCartTab', app.tabs[0].contentKey);
            }
        },
        changeCurrencySub(){
            this.getSubscribeData()
        }
    },

}
</script>
