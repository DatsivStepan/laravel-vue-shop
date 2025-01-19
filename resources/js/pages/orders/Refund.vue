<template>
    <div class="checkout-page row">
        <div class="col-12">
            <div class="content-header clearfix">
                <h2>
                    {{ translate('orders.refund') }}
                </h2>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row" v-if="order.id">
                <div class="col-lg-8  order-0 order-lg-1">
                    <div class="content-folder">
                        <div class="tabs-content">
                            <component :is="'checkout-refund'" :order="order"></component>
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
    </div>
</template>

<script>
import '@sass/checkout.scss';
import {CheckoutRefund, RefundPayBlock} from '@component/orders';

export default {
    components: {
        CheckoutRefund,
        RefundPayBlock
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
        getOrderData() {
            let app = this;

            let params = {
                order_id: this.order_id
            }

            axios.get('/orders/get-order-data', {params: params})
                .then(function (resp) {
                    app.order = resp.data;
                })
                .catch(function () {
                    console.error("error get orders");
                });
        }
    }
}
</script>
