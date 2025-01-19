<template>
    <div>
        <div class="content-header">
            <h2>
                {{translate('system.discounts')}}
            </h2>
            <router-link :to="{name: 'admin-discount-create'}" class="btn float-right product-button-active">
                {{translate('system.create_new')}}
            </router-link>

        </div>
        <div class="content-table">
            <div class="panel">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table-grid v-bind:gridData="modelsData.gridData" :data="modelsData" v-on:get-models="getModels"></table-grid>
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
            translatePath: 'discounts',
            modelsData: {},
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
                filter: filters,
                page: page,
                per_page: perPage,
                sort: sort
            };

            axios.get('/admin/discounts', {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error(resp);
            });
        }
    }
}
</script>
