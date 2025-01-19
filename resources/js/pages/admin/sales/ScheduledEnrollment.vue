<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate('orders.scheduled_enrollment.name')}}
            </h2>
        </div>

        <div class="card shiping-card" style="margin-top: 20px">
            <div class="card-body">
                <table-grid
                    baseUrl="/admin/schuduled-enrollment"
                    :gridData="modelsData.gridData"
                    :data="modelsData"
                    @get-models="getModels"
                ></table-grid>
                    <!-- @print-invoice="printInvoice"
                    @repeat-email="repeatEmail" -->
            </div>
        </div>
    </div>
</template>
<script>
import * as qs from "qs";
export default {
    data: function () {
        return {
            modelsData: {},
            url: '/admin/scheduled-enrollment'
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

            filters.scheduled_enrollment = 1;

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
    }
}
</script>
