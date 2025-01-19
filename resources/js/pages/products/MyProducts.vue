<template>
    <component
        v-if="productsData"
        :is="themeComponent"
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
            pagePath: '/pages/products/MyProducts',
            productsData: null,
        }
    },
    mounted() {
        let app = this;

        app.getProducts({page:1});
    },
    computed: {
        themeComponent: function() {
            let app = this;
            return () => import(`@themes/` + app.getSettingValue('theme') + app.pagePath)
                .then((m) => m.default)
                .catch(() => import(`@themes/default` + app.pagePath));
        }
    },
    watch: {
        $route(to, from) {
            let app = this;
            app.getProducts({page: 1});
        }
    },
    methods: {
        getProducts(params, loadMore) {

            let app = this;
            let filter = {
                my_products: app.$auth.user().id
            };

            if (params.filter) {
                for (const fKey in params.filter) {
                    let ff = params.filter[fKey];
                    filter[fKey] = Array.isArray(ff) ? ff.join() : ff;
                }
            }

            params.filter = filter;

            axios.get(api.urls.catalog.getMyProducts, {params: params,
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
