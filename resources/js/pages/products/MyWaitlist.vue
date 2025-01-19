<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate('products.my_waitlist')}}
            </h2>
        </div>

        <div class="card" style="margin-top: 20px">
            <div class="card-body">
                <table-grid
                    :baseUrl="baseUrl"
                    :gridData="modelsData.gridData"
                    :data="modelsData"
                    @get-models="getModels"
                ></table-grid>
            </div>
        </div>
    </div>
</template>
<script>
import * as qs from "qs";
export default {
    data: function () {
        let app = this;
        return {
            modelsData: {},
            baseUrl: '/products-waitlist'
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
