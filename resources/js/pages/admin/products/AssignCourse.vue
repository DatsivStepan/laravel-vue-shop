<template>
    <div>
        <div v-if="product">

            <div class="content-header ">
                <h2 class="assign-header">
                    {{translate('products.view.assign_courses')}}
                </h2>
            </div>

            <div class="card assign-card">
                 <h3 class="assign-header-for">
                    {{translate('products.assign_course_for')}} {{product.name}}
                </h3>
                <div class="card-body">
                    <assign-form v-if="product" :product="product" @save-form="saveForm" ref='form'></assign-form>
                </div>
            </div>

            <div class="card assign-body" style="margin-top: 20px">
                <div class="card-body">
                    <table-grid :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AssignForm from '@component/courses/AssignForm.vue';
import '@sass/component/admin-area.scss';
import '@sass/component/table.scss';
import * as qs from "qs";

export default {
    components: {
        AssignForm
    },
    data: function () {
        let app = this;
        return {
            product: null,
            modelsData: {},
       }
    },
    mounted() {
        this.getProductData();
        this.getModels();
    },
    methods: {
        getProductData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/products/' + id)
                .then(function (resp) {
                    app.product = resp.data.data;
                })
                .catch(function () {
                    console.error("Error get product id:" + id);
                });
        },
        getModels(page = 1, sort = '', filters = {}, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            filters.product_id = app.$route.params.id;
            let params = {
                filter: filters,
                page: page,
                perPage: perPage,
                sort: sort
            }

            axios.get('/admin/product-assign-course', {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error('Error get courses');
            });
        },
        saveForm(newAssign) {
            let app = this;
            axios.post('/admin/product-assign-course', newAssign)
                .then(function (resp) {
                    app.getModels();
                    app.$refs.form.clearForm();
            })
            .catch(function (resp) {
               console.error(resp);
            });
        },
    }
}
</script>

getModels(page = 1, sort = '', filters = {}, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            let params = {
                filter: filters,
                page: page,
                perPage: perPage,
                sort: sort
            };

            axios.get('/admin/coupons', {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.log(resp);
            });
        },
