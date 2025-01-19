<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{ translate('products.catalogproducts') }}
            </h2>
        </div>

        <featured-slider  v-if="featuredProducts.length" :products="featuredProducts"></featured-slider>

        <div class="panel panel-filter">
            <div class="row align-items-center m-0">
                <div class="col p-0 panel-align" v-if="getSettingValue('display_catalog_price_filter')">
                    <span>{{ translate('products.catalog_price_filter') }}</span>
                    <div class="panel-align__filter">
                        <vue-range-slider
                            :min="filterPriceConfig.min"
                            :max="filterPriceConfig.max"
                            v-model="filters.price"
                            :lazy="true"
                            :formatter="getUserSettingValue('currency_symbol') + '{value}'"
                        >
                        </vue-range-slider>
                    </div>
                </div>
                <div class="col col-custom p-0" v-if="getSettingValue('display_catalog_search_filter')">
                    <div class="search-input">
                        <input type='text' id="filterSearch" class="form-control" v-model="filters.search"
                               :placeholder="translate('products.search')">
                        <ion-icon name="search" class="input-icon"></ion-icon>
                    </div>
                </div>
                <div class="col-auto mob-filter pr-0">
                    <div class="row align-items-center">
                        <!-- <div class="col-auto">
                            <b-dropdown right   menu-class="filter-button">
                                <template #button-content class="filter-button">
                                   <span class="icon-filter"></span>
                                </template>
                                <div class="col-12">
                                    <h2>Filter products</h2>
                                </div>
                                <div class="col-12 mb-35">
                                    <label for="">Select </label>
                                   <div class="form-datepicker">
                                       <input type="text">
                                       <ion-icon name="calendar"></ion-icon>
                                   </div>
                                </div>
                                <div class="col-12 mb-35">
                                    <label for="">Select </label>
                                    <div class="form-datepicker">
                                        <input type="text">
                                        <ion-icon name="calendar"></ion-icon>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-35" v-if="getSettingValue('display_catalog_instructor_filter')">
                                    <custom-multiselect
                                        @update="filters.instructors = $event;"
                                        :searchData="filterInstructorsConfig.search"
                                        :selectedItems="filters.instructors"
                                        :config="filterInstructorsConfig.config" ref="intructors"
                                        :placeholder="translate('products.filtered.by_instructors')"
                                    >
                                    </custom-multiselect>
                                </div>
                                <div class="col-sm-12">
                                    <custom-multiselect
                                        @update="filters.custom_fields = $event;"
                                        :searchData="filterCustomFieldsConfig.search"
                                        :selectedItems="filters.custom_fields"
                                        :config="filterCustomFieldsConfig.config" ref="custom_fields"
                                        :placeholder="translate('products.filtered.by_custom_fields')"
                                    >
                                    </custom-multiselect>
                                </div>
                                <div class="col-sm-12" v-if="filters.custom_fields.length">
                                    <input type='text' id="customFieldSearch" class="form-control" v-model="filters.custom_fields_search"
                                           :placeholder="translate('custom_fields.search')">

                                </div>
                            </b-dropdown>
                        </div> -->
                        <div class="col-auto pr-0">
                            <div class="d-flex button-view-template align-items-center">
                                <button :class="productView === 'grid' ? 'active': ''" @click="productView = 'grid'"><span class="icon-grid"></span></button>
                                <button :class="productView === 'list' ? 'active': ''" @click="productView = 'list' + 'pr-0'"><span class="icon-list"></span></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="line row"></div>
        <div class="row" :class="productView === 'grid' ? 'grid-view': 'list-view'">
            <product-item v-for="product in products" :key="product.id" :product="product"></product-item>
        </div>

        <div class="container padding1" v-if="isLoadMore && viewLoadMore">
            <div class="row">
                <div class="col text-center">
                    <button class="load-more" @click="loadMore">
                        {{ translate('system.load_more') }}
                    </button>
                </div>
            </div>
        </div>

        <pagination
            v-if="isPages"
            class="padding0 justify-content-end"
            :data="productsData"
            @pagination-change-page="readProducts"
            :limit="1">
        </pagination>
    </div>

</template>

<script>
import '../../sass/catalog.scss';
import 'vue-range-component/dist/vue-range-slider.css';
import FeaturedSlider from '../../components/products/FeaturedSlider.vue';
import CustomMultiselect from '@component/form/CustomMultiselect.vue';
import ProductItem from '../../components/products/ProductItem.vue';

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
        VueRangeSlider: () => import('vue-range-component'),
        FeaturedSlider,
        CustomMultiselect,
        ProductItem
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
        isPages() {
            return this.getSettingValue('load_more_action') === 'pages'
        },
        isLoadMore() {
            return this.getSettingValue('load_more_action') === 'load_more'
        },
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
