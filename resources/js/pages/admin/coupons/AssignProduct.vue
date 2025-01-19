<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('products.assign_products')}} {{coupon.code}}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <assign-form v-bind:product="coupon" :refreshTable="getModels"></assign-form>
            </div>
        </div>

        <div class="card" style="margin-top: 20px">
            <div class="card-body">
                <h4>
                    {{translate('products.assigned_products')}}
                </h4>

                <table-grid v-bind:gridData="modelsData.gridData" :data="modelsData" v-on:get-models="getModels"></table-grid>

            </div>
        </div>
    </div>
</template>

<script>
import AssignForm from './AssignProductForm.vue';
import '@sass/component/product-btns.scss';

export default {
    components: {
        AssignForm
    },
    data: function () {
        return {
            coupon: {
                id: 0,
                code: '',
                products: [],
            },
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

            axios.get('admin/coupons/' + id)
                .then(function (resp) {
                    app.coupon = resp.data.data;
                })
                .catch(function () {
                    console.error("Error get discount id:" + id);
                });
        },
        getModels(page = 1, sort = null, filters = null, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            let couponId = app.$route.params.id;
            let params = {
                page: page,
                coupon_id: couponId,
                filters: filters
            }

            axios.get('/admin/coupons/get-assigned-products', {params: params}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error('Error get courses');
            });
        }
    }
}
</script>
