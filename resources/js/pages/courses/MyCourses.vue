<template>
    <component
        v-if="productsData"
        :is="themeComponent"
        :featuredProducts="featuredProducts"
        :productsData="productsData"

        @get-products="getProducts"

        ref="theme_component"
        ></component>
</template>

<script>
import api from '@/api';
import * as qs from "qs";

export default {
    data() {
        return {
            pagePath: '/pages/products/Catalog',
            //props
            featuredProducts: [],
            productsData: null,
            categoryId: null
            //props
        }
    },
    mounted() {
        let app = this;

        if (app.getSettingValue('enable_featured_products')) {
            app.getFeaturedProducts();
        }
        app.getProducts({page:1});
    },
    computed: {
        themeComponent: function() {
            let app = this;
            return () => import(`@themes/` + app.getSettingValue('theme') + app.pagePath);
        }
    },
    watch: {
        $route(to, from) {
            let app = this;
            app.categoryId = app.$route.params.id;
            app.getProducts({page: 1});
        }
    },
    methods: {
        getFeaturedProducts() {
            let app = this;
            api.get(api.urls.catalog.getFeaturedProducts).then(response => {
                app.featuredProducts = response.data.data;
            }).catch(function (resp) {
                console.error(resp);
            });
        },
        getProducts(params, loadMore) {
            let app = this;
            params.filter = {};
            if (params.filter) {
                let filters = {};
                for (const fKey in params.filter) {
                    let ff = params.filter[fKey];
                    filters[fKey] = Array.isArray(ff) ? ff.join() : ff;
                }
                params.filter = filters;
            }

            params.filter.categories = app.categoryId;

            axios.get(api.urls.catalog.getProducts, {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                if (loadMore) {
                    let products = app.productsData.data;
                    app.productsData = response.data;
                    app.productsData.data = products.concat(response.data.data);
                } else {
                    app.productsData = response.data;
                }
            }).catch(function (resp) {
                console.error(resp);
            });
        },
    }
}
</script>
