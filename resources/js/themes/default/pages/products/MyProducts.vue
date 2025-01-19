<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{ translate('products.my_products') }}
            </h2>
        </div>

        <div class="panel">
            <div class="row align-items-center justify-content-between">
                <div class="col-6 cutom-detalis-filter" v-if="getSettingValue('display_catalog_search_filter')">
                    <div class="search-input">
                        <input type='text' id="filterSearch" class="form-control" v-model="filters.search"
                               :placeholder="translate('products.search')">
                        <ion-icon name="search"></ion-icon>
                    </div>
                </div>
                <div class="col-auto mob-filter">
                    <div class="row align-items-center">
                        <div class="col-auto pr-0">
                            <div class="d-flex button-view-template align-items-center">
                                <button :class="productView === 'grid' ? 'active': ''" @click="productView = 'grid'"><span class="icon-grid"></span></button>
                                <button :class="productView === 'list' ? 'active': ''" @click="productView = 'list'"><span class="icon-list"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="line row"></div>
        <div class="row" :class="productView === 'grid' ? 'grid-view': 'list-view'">
            <product-item v-for="product in products" :key="product.id" :product="product" :type="1"></product-item>
        </div>

        <div class="container padding1" v-if="viewLoadMore">
            <div class="row">
                <div class="col text-center">
                    <button class="btn btn-primary my-product-btn" @click="loadMore">
                        {{ translate('system.load_more') }}
                    </button>
                </div>
            </div>
        </div>

        <pagination
            class="padding1"
            :data="productsData"
            @pagination-change-page="readProducts"
            align="center"
            :limit="2">
        </pagination>
    </div>

</template>

<script>
import '../../sass/catalog.scss';
import 'vue-range-component/dist/vue-range-slider.css';
import ProductItem from '../../components/products/ProductItem.vue';
import FeaturedSlider from '../../components/products/FeaturedSlider.vue';
import CustomMultiselect from '@component/form/CustomMultiselect.vue';
import VueRangeSlider from 'vue-range-component';

export default {
    props: {
        featuredProducts: {
            type: Array,
            default: () => ([])
        },
        productsData: {
            type: Object,
            required: true
        },
    },
    components: {
        VueRangeSlider,
        ProductItem,
        FeaturedSlider,
        CustomMultiselect
    },
    data() {
        let app = this;
        return {
            productView: 'grid',
            instructors: [],
            filters: {
                search: '',
                price: [app.productsData.minPrice, app.productsData.maxPrice],
                instructors: [],
                custom_fields: [],
                custom_fields_search: ''
            },
            filterInstructorsConfig: {
                config:{
                    mountedSearch: true
                },
                search: {
                    url: '/products/get-users-array'
                }
            },
            filterCustomFieldsConfig: {
                config:{
                    mountedSearch: true
                },
                search: {
                    url: '/products/get-custom-fields-array'
                }
            },
        }
    },
    computed: {
        products: {
            get: function () {
                let app = this;
                return app.productsData.data;
            }
        },
        filterPriceConfig: {
            get: function () {
                let app = this;
                return {
                    min: app.productsData.minPrice,
                    max: app.productsData.maxPrice,
                }
            }
        },
        viewLoadMore() {
            return !(this.productsData.meta.current_page === this.productsData.meta.last_page);
        }
    },
    watch: {
        filters: {
            handler: function(val, oldVal) {
                this.readProducts();
            },
            deep: true
        },
    },
    methods: {
        loadMore() {
            let app = this;
            app.productsData.meta.current_page++;
            app.readProducts(app.productsData.meta.current_page, true);
        },
        readProducts(page, loadMore = false) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            let params = {
                page: page,
                filter: {
                    search: app.filters.search,
                    price: app.filters.price,
                    instructors: app.filters.instructors.map(field => field.value),
                    custom_fields: app.filters.custom_fields.map(field => field.value),
                    custom_fields_search: app.filters.custom_fields_search
                }
            }

            app.$emit('get-products', params, loadMore);
        }
    }
}
</script>
