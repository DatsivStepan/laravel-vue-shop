<template>
    <div>
        <div v-if="vendor">
            <div class="content-header">
               <div class="content-align">
                    <h2>
                        {{translate('vendors.assign_users_for')}} {{vendor.name}}
                    </h2>
               </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <assign-form :vendor="vendor" @save-form="saveForm" ref='form'></assign-form>
                </div>
            </div>

            <div class="card" style="margin-top: 20px">
                <div class="card-body">
                    <h4>
                        {{translate('vendors.assigned_users')}}
                    </h4>

                    <table-grid :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AssignForm from '@component/vendors/AssignUsersForm.vue';
import * as qs from "qs";
import '@sass/component/product-btns.scss';
import '@sass/component/admin-area.scss'

export default {
    components: {
        AssignForm
    },
    data: function () {
        let app = this;
        return {
            vendor: null,
            modelsData: {},
       }
    },
    mounted() {
        this.getVendorData();
        this.getModels();
    },
    methods: {
        getVendorData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/vendors/' + id)
                .then(function (resp) {
                    app.vendor = resp.data.data;
                })
                .catch(function () {
                    console.error("Error get vendor id:" + id);
                });
        },
        getModels(page = 1, sort = '', filters = null, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            let vendorId = app.$route.params.id;
            let params = {
                page: page,
                sort: sort,
                perPage: perPage,
                filter: filters
            };

            axios.get('/admin/vendors-users/' + vendorId, {params: params,
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
            axios.post('/admin/vendors-users/assign', newAssign)
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
