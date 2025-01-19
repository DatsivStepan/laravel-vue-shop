<template>
    <div>
        <div v-if="role">
            <div class="content-header">
                <div class="content-align">
                    <h2>
                        {{translate('roles.lms_assign.assign_lms_role_for')}} {{role.name}}
                    </h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <assign-form v-if="role" :role="role" @get-models="getModels" ref='form'></assign-form>
                </div>
            </div>

            <div class="card" style="margin-top: 20px">
                <div class="card-body">
                    <h4>
                        {{translate('roles.lms_assign.assigned_roles')}}
                    </h4>

                    <table-grid :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AssignForm from '@component/roles/AssignLmsRoleForm.vue';
import '@sass/component/product-btns.scss';
import * as qs from "qs";

export default {
    components: {
        AssignForm
    },
    data: function () {
        let app = this;
        return {
            role: null,
            modelsData: {},
       }
    },
    mounted() {
        this.getModel();
        this.getModels();
    },
    methods: {
        getModel() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/roles/' + id)
                .then(function (resp) {
                    app.role = resp.data.data;
                })
                .catch(function () {
                    console.error("Error get role id:" + id);
                });
        },
        getModels(page = 1, sort = '', filters = {}, perPage = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            filters.role_id = app.$route.params.id;
            let params = {
                page: page,
                filter: filters
            }
            axios.get('/admin/roles-lms', {params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error('Error get courses');
            });
        }
    }
}
</script>
