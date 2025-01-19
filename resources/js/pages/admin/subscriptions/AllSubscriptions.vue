<template>
    <div>
        <div class="content-header">
            <h2>
                {{ translate('system.menu.subscriptions') }}
            </h2>
        </div>
        <div class="content-table">
            <div class="panel">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table-grid v-bind:gridData="modelsData.data" :data="modelsData"
                                    v-on:get-models="getModels"></table-grid>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as qs from "qs";
import api from "@/api";

export default {
    data: function () {
        let app = this;
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
                page: page,
                per_page: perPage,
            };

            axios.get(api.urls.admin.subscriptions.all, {
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
