<template>
    <table-grid
        :baseUrl="baseUrl"
        :gridData="modelsData.gridData"
        :data="modelsData"
        @print-invoice="printInvoice"
        @repeat-email="repeatEmail"
        @get-models="getModels"
        :isAll="isSales"
    ></table-grid>
</template>
<script>
import * as qs from "qs";

export default {
    props: {
        url: {
            type: String
        },
        isSales: {
            type: Boolean
        }
    },
    data: function () {
        let app = this;
        return {
            modelsData: {},
            baseUrl: app.url
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

            axios.get(app.url, {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error(resp);
            });
        },
        repeatEmail(params) {
            let app = this;
            axios.get('/admin/sales/repeat-email/' + params.id)
                .then(function (resp) {
                    if (resp.data.status) {
                        app.$alert(app.translate('orders.repeat_email_sended'), '', 'error');
                    }
                })
                .catch(function () {
                    console.error("print error");
                });
        },
    }
}
</script>
