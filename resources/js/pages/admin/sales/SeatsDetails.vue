<template>
    <div>
        <div class="content-header">
           <div class="content-align">
                <h2>
                    {{translate('orders.seats_details.name')}}
                </h2>
           </div>
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
        return {
            modelsData: {},
            baseUrl: '/admin/seats/details',
            seat: {}
        }
    },
    computed: {
        id: function() {
            return this.$route.params.id;
        }
    },
    mounted() {
        this.getModel();
        this.getModels();
    },
    methods: {
        getModel() {
            let app = this;
            axios.get('/admin/seats/' + app.id).
            then(response => {
                app.seat = response.data;
            }).catch(function (resp) {
                console.error(resp);
            });
        },
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

            axios.get('/admin/seats/details/' + app.id, {params: params,
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
