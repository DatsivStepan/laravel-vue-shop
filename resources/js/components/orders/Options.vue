<template>

    <div class="options-data">

        <div class="options-data-list">
            <b-form-radio-group
                id="radio-group-1"
                v-model="billingType"
                :options="billingTypeOption1"
                name="radio-options"
            ></b-form-radio-group>
            <template v-if="isRegular">
                <div class="d-flex align-items-center justify-content-between options-data-item">
                    <span class="options-data-item-label">{{ translate('orders.subtotal') }}</span>
                    <span class="options-data-item-number">{{ order.formatted_subtotal }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-item">
                    <span class="options-data-item-label">{{ translate('discounts.discount') }}</span>
                    <span class="options-data-item-number">{{ order.formatted_discount }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-item"
                     v-if="getSettingValue('enable_taxes') == 1">
                    <span class="options-data-item-label">{{ order.tax.label }}</span>
                    <span class="options-data-item-number">{{ order.tax.value }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-total">
                    <span class="options-data-total-label">{{ translate('orders.total') }}</span>
                    <span class="options-data-total-number">{{ order.formatted_total }}</span>
                </div>
            </template>
        </div>
        <div class="options-data-list" v-if="isShowInstallmentSwitch">
            <b-form-radio-group
                id="radio-group-1"
                v-model="billingType"
                :options="billingTypeOption2"
                name="radio-options"
            ></b-form-radio-group>
            <template v-if="isInstallment">
                <div class="d-flex align-items-center justify-content-between options-data-item">
                    <span class="options-data-item-label">{{ translate('orders.installments.recurring_period') }}</span>
                    <span class="options-data-item-number">{{ order.recurring_period }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-item">
                    <span class="options-data-item-label">{{ translate('orders.installments.billing_cycles') }}</span>
                    <span class="options-data-item-number">{{ order.billing_cycles }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-item">
                    <span class="options-data-item-label">{{ translate('orders.installments.price') }}</span>
                    <span class="options-data-item-number">{{ order.formatted_installment_price }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-item">
                    <span class="options-data-item-label">{{ translate('orders.installments.fee') }}</span>
                    <span class="options-data-item-number">{{ order.formatted_fee }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-item"
                     v-if="getSettingValue('enable_taxes') == 1">
                    <span class="options-data-item-label">{{ order.tax.label }}</span>
                    <span class="options-data-item-number">{{ order.tax.value }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between options-data-total">
                    <span class="options-data-total-label">{{ translate('orders.total') }}</span>
                    <span class="options-data-total-number">{{ order.formatted_total }}</span>
                </div>
            </template>
        </div>


        <div v-if='tabs' class="buttons-checkout-options">
            <checkout-pagination :tabs="tabs" :in="'options'" @plus-tab="plusTab"
                                 @minus-tab="minusTab" ref="pagination"></checkout-pagination>
        </div>

    </div>

</template>

<script>
import CheckoutPagination from './Pagination.vue';

export default {
    components: {
        CheckoutPagination
    },
    props: {
        order: {
            type: Object
        },
        tabs: {
            type: Array
        },
    },
    data: function () {
        return {
            billingType: this.order.billingType,
            billingTypeOption1: [
                {text: this.translate('products.billing_type.regular'), value: 'regular'},
            ],
            billingTypeOption2: [
                {text: this.translate('products.billing_type.installment'), value: 'installment'},
            ]
        }
    },
    watch: {
        billingType(val, oldVal) {
            if (val !== oldVal) {
                this.$emit('get-order-data', val);
            }
        }
    },
    computed: {
        isRegular() {
            return this.billingType === 'regular';
        },

        isShowInstallmentSwitch() {
            return this.order.products.length === 1 && this.order.products[0].billing_type === 'installment' && this.getSettingValue('enable_installment') == 1
        },

        isInstallment() {
            return this.billingType === 'installment' && this.getSettingValue('enable_installment') == 1;
        }
    },
    methods: {
        plusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        minusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
    }
}
</script>
