<template>
    <div>
        <breadcrumb-category :model="category"></breadcrumb-category>

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
import BreadcrumbCategory from '@component/categories/BreadcrumbCategory.vue';
import * as qs from "qs";

export default {
    components: {
       BreadcrumbCategory
    },
    data() {
        return {
            parentId: 0,
            translatePath: 'categories',
            modelsData: {},
            baseUrl: '/admin/categories',
            category: null
        }
    },
    mounted() {
        this.getModels();
    },
    watch: {
        $route(to, from) {
            let app = this;
            app.parentId = app.$route.params.id;
            app.getModels();
            app.getModel();
        }
    },
    methods: {
        getModel() {
            let app = this;
            if (app.parentId) {
                axios.get(app.baseUrl + '/' + app.parentId).then(response => {
                    app.category = response.data.data;
                }).catch(function (resp) {
                    console.error(resp);
                });
            } else {
                app.category = null;
            }
        },
        getModels(page = 1, sort = '', filters = {}, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            if (app.parentId) {
                filters.parent_id = app.parentId;
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
