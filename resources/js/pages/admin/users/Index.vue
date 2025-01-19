<template>
    <div>
    <div class="content-header">
            <h2>
                {{translate('system.users')}}
            </h2>
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

export default {
    data() {
        return {
            translatePath: 'users',
            modelsData: {},
            baseUrl: '/admin/users',
            category: null
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

            axios.get(app.baseUrl, {params: params,
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
