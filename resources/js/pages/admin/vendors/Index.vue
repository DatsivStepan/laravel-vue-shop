<template>
    <div>
        <div class="content-header">
            <h2>
                {{translate(translatePath + '.vendors')}}
            </h2>
            <router-link :to="{name: 'admin-vendors-create'}" class="btn product-button-active">
                {{translate(translatePath + '.create_new')}}
            </router-link>
        </div>
        <div class="content-table">
            <div class="panel">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table-grid :baseUrl="baseUrl" :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as qs from "qs";
import '@sass/component/product-btns.scss';

export default {
    data: function () {
        let app = this;
        return {
            translatePath: 'vendors',
            modelsData: {},
            baseUrl: '/admin/categories'
        }
    },
    mounted() {
        this.getModels();
    },
    methods: {
        getModels(page = 1, sort = '', filters = {}, perPage = null) {
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

            axios.get('/admin/vendors', {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error(resp);
            });
        },
    }
}
</script>
