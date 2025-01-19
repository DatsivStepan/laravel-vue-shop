<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{ translate('products.my_favorites') }}
            </h2>
        </div>

        <div class="panel">
            <div class="row align-items-center">
                <div class="col-sm-3 panel-align" v-if="getSettingValue('display_catalog_price_filter')">
                    <span>{{ translate('products.catalog_price_filter') }}</span>
                    <div class="panel-align__filter">
                        <vue-range-slider :min="filterPriceConfig.min" :max="filterPriceConfig.max"
                                      @drag-end="readProducts(1)" v-model="filters.price"></vue-range-slider>
                    </div>
                </div>
                <div class="col" v-if="getSettingValue('display_catalog_search_filter')">
                    <div class="search-input">
                        <input type='text' id="filterSearch" class="form-control" v-model="filters.search"
                               :placeholder="translate('products.search')">
                        <ion-icon name="search"></ion-icon>
                    </div>
                </div>
                <div class="col-auto mob-filter">
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
                                <button :class="productView === 'list' ? 'active': ''" @click="productView = 'list'"><span class="icon-list"></span></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="line row"></div>
        <div class="row">
            <product-item v-for="product in products" :key="product.id" :product="product"></product-item>
        </div>

        <div class="container padding1" v-if="viewLoadMore">
            <div class="row">
                <div class="col text-center">
                    <button class="btn btn-primary" @click="loadMore">
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
