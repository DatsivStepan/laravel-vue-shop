<template>
    <div>
        <div class="content-header">
            <h2>
                {{translate(translatePath + '.products')}}
            </h2>
            <div class="d-flex">
                <router-link  if="checkPermission(['manageCustomFields']) && getSettingValue('enable_products_custom_fields')"
                    :to="{name: 'admin-product-create'}" class="btn float-right product-button">
                    {{translate(translatePath + '.create_new')}}
                </router-link>

                <div class="col-auto pl-0"></div>

                <router-link :to="{name: 'admin-custom-fields', params: { instanceType: 'product' }}" class="btn float-right product-button-active">

                    {{translate(translatePath + '.custom_fields')}}
                </router-link>
            </div>

        </div>

        <div class="content-table">
            <div class="panel">
                <div class="table-container">
                    <table-grid :baseUrl="baseUrl" :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import '@sass/component/product-btns.scss'
import '@sass/component/admin-area.scss'
import * as qs from "qs";
export default {
    data: function () {
        let app = this;
        return {
            translatePath: 'products',
            modelsData: {},
            baseUrl: '/admin/products'
        }
    },
    mounted() {
        this.getModels();
    },
    methods: {
        getModels(page, sort = '', filters = {}, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            let params = {
                page: page,
                sort: sort,
                filter: filters,
                perPage: perPage
            };

            axios.get('/admin/products', {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.log(resp);
            });
        },
    }
}
</script>
