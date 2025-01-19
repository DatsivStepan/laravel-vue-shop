<template>
    <div>
        <form v-on:submit="saveForm()">

            <div class="form-group row">
                <label for="products" class="col-sm-3">{{translate('products.select_product')}}:</label>
                <div class="col-sm-9">
                    <custom-multiselect
                        @update="onSelect($event)"
                        :searchData="searchData"
                        :selectedItems="selectProducts"
                        :config="config"
                        ref="multiselect_category"
                    >
                    </custom-multiselect>
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
                url: '/admin/discounts/get-products-for-assign',
                params: {discount_id: this.$route.params.id}
            },
            selectProducts: [],
            assign: {
                discount_id: 0,
                products: [],
                assign_all: false,
            },
            products: [],
            pickerConfig: {
                enableTime: true,
                altFormat: "F j, Y H:i",
                altInput: true
            }
        }
    },
    watch: {
        assign: {
            handler: function (newValue, oldVal) {
                if (newValue.assign_all && !oldVal.assign_all) {
                    this.selectProducts = {...this.products}
                }
            },
            deep: true
        }
    },
    methods: {
        saveForm() {
            event.preventDefault();

            let app = this;
            app.assign.discount_id = app.$route.params.id;
            let newAssign = app.assign;

            axios.post('/admin/discounts/assign-products', newAssign)
                .then(function (resp) {
                    app.refreshTable();
                    app.reset()
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
    },
    computed: {
        config() {
            return {
                multiple: true,
                searchAjax: true,
                closeOnSelect: false,
                selectAll: {
                    show: true,
                    title: this.translate('products.select_all_products')
                },
                mountedSearch: true,
            }
        }
    }
}
</script>
