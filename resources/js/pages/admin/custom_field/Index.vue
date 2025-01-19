<template>
    <div>
        <div class="content-header">
            <h2>
                {{translate(translatePath + '.custom_fields')}}
            </h2>
            <router-link :to="{name: 'admin-custom-fields-create', params: { instanceType: instanceType }}" class="btn float-right product-button-active">
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
        return {
            instanceType : '',
            translatePath: 'custom_fields',
            modelsData: {},
            baseUrl: '/admin/custom-fields'
        }
    },
    mounted() {
        let app = this;
        app.getModels();
        app.instanceType = app.$route.params.instanceType;
    },
    methods: {
        getModels(page = 1, sort = null, filters = {}, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            filters.parentId = app.parentId;
            filters.instance_type = app.instanceType;

            let params = {
                page: page,
                sort: sort,
                filter: filters,
                perPage: perPage,
            };

            axios.get('/admin/custom-fields', {params: params,
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
