<template>
    <div>
        <div class="content-header">
            <h2>
                {{ translate('notifications.notifications') }}
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
import api from "../../../api";
import * as qs from "qs";

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

            let filter = {};
            if (filters && filters.search) {
                filter.subject = filters.search
            }

            let params = {
                filter: filter,
                page: page,
                per_page: perPage,
            };

            axios.get(api.urls.admin.notifications.index, {
                params: params, paramsSerializer: params => {
                    return qs.stringify(params)
                }
            }).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error(resp);
            });
        },
    }
}
</script>
