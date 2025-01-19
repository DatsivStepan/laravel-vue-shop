<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('vendors.assignvendors')}}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <assign-form v-bind:instanceType="type" v-bind:instanceId="id" ref="form" v-on:reload-data="reloadData"></assign-form>
            </div>
        </div>

        <div class="card" style="margin-top: 20px">
            <div class="card-body">
                <h4>
                    {{translate('vendors.vendors')}}
                </h4>

                <assign-table v-bind:instanceType="type" v-bind:instanceId="id" ref="table" v-on:reload-data="reloadData"></assign-table>
            </div>
        </div>
    </div>
</template>

<script>
import AssignForm from '@component/vendors/AssignForm.vue';
import AssignTable from '@component/vendors/AssignTable.vue';
import '@sass/component/product-btns.scss'

export default {
    components: {
        AssignForm,
        AssignTable
    },
    data: function () {
        return {
            type: 3,
            id: 0,
        };
    },
    mounted() {
        let app = this;
        app.id = app.$route.params.id;
    },
    methods: {
        reloadData() {
            this.$refs.form.getVendorsOptions();
            this.$refs.table.getModels(1);
        }
    }
}
</script>
