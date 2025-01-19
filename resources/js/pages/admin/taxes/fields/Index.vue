<template>
    <div>
        <div class="content-header">
            <h2>
                {{ translate('taxes.taxes') }}
            </h2>
            <router-link :to="{name: 'admin-advance-taxes-create'}"
                         class="btn float-right product-button-active">
                {{ translate('system.create_new') }}
            </router-link>

        </div>
        <div class="content-table">
            <div class="panel">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table-grid v-bind:gridData="modelsData.gridData" :data="modelsData"
                                    v-on:get-models="getModels"></table-grid>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as qs from "qs";
import api from "../../../../api";

export default {
    data: function () {
        return {
            modelsData: {},
        }
    },
    mounted() {
        this.getModels();
    },
    methods: {
        getModels(page = 1, sort = {}, filters = {}, perPage = null) {
            let app = this;

            if (typeof page === 'undefined') {
                page = 1;
            }

            let params = {
                field_id: app.$route.params.id,
                page: page,
                sort: sort,
                per_page: perPage,
            };

            axios.get(api.urls.admin.taxes.fields.index, {
                params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }
            }).then(response => {
                app.modelsData = response.data;


            }).catch(function (resp) {
                console.error(resp);
            });
        }
    }
}
</script>
