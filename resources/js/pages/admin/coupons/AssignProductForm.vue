<template>
    <div>
        <form v-on:submit="saveForm()">
            <div class="form-group row">
                <div class="col-6">
                <label for="products" class="form-control-label">{{ translate('products.assign_products') }}:</label>
                    <custom-multiselect
                        @update="onSelect($event)"
                        :searchData="searchData"
                        :selectedItems="selectProducts"
                        :config="{multiple: true, searchAjax: true, closeOnSelect: false}"
                        ref="multiselect_category"
                    >
                    </custom-multiselect>
                <b-checkbox class="col" v-model="assign.assign_all">{{ translate('products.select_all_products') }}
                    </b-checkbox>
                </div>
            </div>
            <button class="btn btn-primary float-right mb-2 assign-btn">{{ translate('system.assign') }}</button>
        </form>
    </div>
</template>

<script>
import _ from 'lodash'
import CustomMultiselect from '@component/form/CustomMultiselect.vue';

import 'vue-search-select/dist/VueSearchSelect.css'

export default {
    components: {
        CustomMultiselect
    },
    props: ['product', 'refreshTable'],
    data: function () {
        return {
            searchData: {
                url: '/admin/coupons/get-products-for-assign',
                params: {coupon_id: this.$route.params.id}
            },
            selectProducts: [],
            assign: {
                coupon_id: 0,
                products: [],
                assign_all: false,
            },
            pickerConfig: {
                enableTime: true,
                altFormat: "F j, Y H:i",
                altInput: true
            }
        }
    },
    methods: {
        saveForm() {
            event.preventDefault();
            let app = this;
            app.assign.coupon_id = app.$route.params.id;
            let newAssign = app.assign;
            axios.post('/admin/coupons/assign-products', newAssign)
                .then(function (resp) {
                    app.reset()
                    app.refreshTable();
                })
                .catch(function (resp) {
                    console.log(resp);
                });
        },

        onSelect(items) {
            this.selectProducts = items;
            this.assign.products = items.map((item) => {
                return item.value
            });

        },
        reset() {
            this.selectProducts = [];
            this.assign.products = [];
        },
        selectFromParentComponent() {
            this.selectProducts = _.unionWith(this.selectProducts, [this.selectProducts[0]], _.isEqual);
        }
    }
}
</script>
