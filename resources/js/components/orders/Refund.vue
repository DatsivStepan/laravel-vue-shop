<template>
    <div>
        <div class="refund-product" v-for="(product, key) in order.products">
            <div class="media-li " >
                <div class="d-flex position-relative">
                    <div class="u-lg-avatar" :style="{backgroundImage:'url(' + product.image_src + ')'}"></div>
                </div>
                <div class="media-body">
                    <h4 class="title-product">{{ product.name }}</h4>
                    <span class="d-block  text-info-product">{{ translate('orders.price') }}:
                        {{ product.formatted_price }}</span>
                </div>
            </div>
            <div class="refund-product-input">
                <input class="refund-input-body" type="number" v-model="product.discount_price" :max="product.price"
                       @change="calculateRefundAmount">
                <input class="table-input" type="checkbox" :id="key" v-model='product.refund' @click="markProductAsRefund(product)">
                <label :for="key"></label>
            </div>
        </div>

        <div class="summary-folder summary-refound">
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
            <div class="d-flex align-items-center justify-content-between total-list total-list-secondary">
                <span class="summary-folder-label">{{ translate('orders.total') }}</span>
                <span class="summary-folder-number">{{ order.formatted_total }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between total-list">
                <span class="summary-folder-label">{{ translate('orders.refunds.refunded') }}</span>
                <span class="summary-folder-number">{{ order.formatted_refunded }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between total-list">
                <span class="summary-folder-label">{{ translate('orders.refunds.maximum_refund_amount') }}</span>
                <span class="summary-folder-number">{{ order.formatted_total }}</span>
            </div>
            <div class="d-flex total-list refund-amount">
                <span class="summary-folder-label">{{ translate('orders.refunds.amount') }}</span>
                <input type="number" class="refund-input" v-model="request.refund_amount">
            </div>
            <div class="manual-refund total-list" v-if="ismManualReturnEnable">
                <input class="table-input" type="checkbox" :id="order.id" v-model='request.is_manual' :max="order.total">
                <label :for="order.id"></label>
                <span class="summary-folder-label manual-refund-title">
                    {{ translate('orders.refunds.manual_refund') }}
                </span>
            </div>
        </div>
        <div class="buttons-checkout-options refund-action">
            <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
                <button type="button" class="btn btn-success refund-btn" @click="showPopup">
                    {{ translate('orders.refunds.refund') }}
                </button>
            </div>
        </div>

        <div class="refund-popup" v-if="request.showPop">
                <div class="refund-popup-inner " :class="activePopup">
                    <h3 class="refund-popup-header">{{ translate('system.confirm.confirm') }}</h3>
                    <p class="refund-popup-title">
                        {{ translate('orders.refunds.confirm_message', {amount: refundAmount}) }}
                    </p>
                    <div class="d-flex refund-popup-label">
                        <label class="manual-refund-title">
                            {{ translate('orders.refunds.reason') }}
                        </label>
                        <textarea v-if="!reasons" v-model="request.reason"></textarea>
                        <v-select
                            v-if="reasons"
                            v-model="request.reason"
                            class="style-chooser"
                            :options="reasons"
                            ></v-select>
                    </div>
                    <div class="buttons-checkout-options d-sm-flex justify-content-sm-end align-items-sm-center">
                        <button class="btn d-block mb-3 mb-sm-0 refund-popup-cancel" @click="hidePopup">
                            {{ translate('system.no') }}
                        </button>
                        <button type="button" class="btn btn-success refund-popup-confirm" @click.prevent="refund()">
                            {{ translate('system.yes') }}
                        </button>
                    </div>
                </div>
        </div>

    </div>

</template>

<script>
import CheckoutPagination from './Pagination.vue';
import api from "@/api";

export default {
    components: {
        CheckoutPagination
    },
    props: ['order'],
    data: function () {
        return {
            request: {
                order_id: this.order.id,
                is_manual: false,
                refund_amount: 0,
                reason: null,
                showPop: false,
                hidePop: true
            },
            reasons: null,
        }
    },
    computed: {
        activePopup() {
            return {
                'swal2-show': this.request.showPop,
                'swal2-hide': this.request.hidePop
            }
        },
        ismManualReturnEnable() {
            return parseInt(this.getSettingValue('enable_manual_refund'));
        },
        refundAmount() {
            return this.getUserSettingValue('currency_symbol') + ' ' + this.request.refund_amount;
        }
    },
    created() {
        if (this.getSettingValue('refund_reasons')) {
            this.reasons = this.getSettingValue('refund_reasons').split('\n')
        }
    },
    methods: {
        markProductAsRefund(product) {
            product.refund = !product.refund;
            this.calculateRefundAmount();
        },

        calculateRefundAmount() {
            let refundAmount = 0;
            this.order.products.map((product) => {
                if (product.refund && product.discount_price > 0) {
                    refundAmount = refundAmount + parseFloat(product.discount_price);
                }
            })

            refundAmount += parseFloat(this.order.tax_amount);

            this.request.refund_amount = refundAmount > this.order.total ? this.order.total : refundAmount;
        },
        showPopup(){
            this.request.showPop = !this.request.showPop;
            this.request.hidePop = !this.request.hidePop;
        },
        hidePopup() {
            this.request.hidePop = !this.request.hidePop;
            setTimeout(() => {
                this.request.showPop = !this.request.showPop;
            }, 200)

        },
        getReasons() {
            let app = this;
            axios.post(api.urls.admin.refunds.getReasons).then(res => {
                app.reasons = res.data;
            })
        },
        refund() {
            axios.post('admin/orders/refund', this.request).then(res => {
                this.$router.push({name: 'admin-sales'});
            }).catch(err => {
                alert(err.message)
            })
        },
    },
}
</script>
