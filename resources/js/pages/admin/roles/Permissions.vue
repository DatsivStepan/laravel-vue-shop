<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{model.name}} {{translate('roles.permissions')}}
                </h2>
            </div>
        </div>

        <div class="content-table">
            <div class="panel">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table-grid :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
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
            model: {
                id: null,
                name: '',
            },
            modelsData: {}
       }
    },
    mounted() {
        this.getRole();
        this.getModels();
    },
    methods: {
        getModels(page, sort = '', filters = null, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }
            let id = app.$route.params.id;

            let params = {
                page: page,
                sort: sort,
                filter: filters,
                perPage: perPage
            };

            axios.get('/admin/permissions/' + id, {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error(resp);
            });
        },
        getRole() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/roles/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.error("error get role id:" + id);
                });
        },
     }
}
</script>
