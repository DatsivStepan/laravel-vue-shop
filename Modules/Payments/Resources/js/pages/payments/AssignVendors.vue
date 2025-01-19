<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{ translate('vendors.assignvendors') }}
                </h2>
            </div>
        </div>

        <div class="card rounded-0">
            <div class="card-body">
                <assign-form :instanceType="type" :instanceId="id" ref="form" @reload-data="reloadData"></assign-form>
            </div>
        </div>

        <div class="card rounded-0" style="margin-top: 20px">
            <div class="card-body">
                <h4>
                    {{ translate('vendors.vendors') }}
                </h4>

                <assign-table :instanceType="type" :instanceId="id" ref="table"
                              @reload-data="reloadData"></assign-table>
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
            type: 4 // payment type in VendorAssign
        };
    },
    computed: {
        id: function () {
            return this.$route.params.id;
        }
    },
    methods: {
        reloadData() {
            this.$refs.table.getModels(1);
        }
    }
}
</script>
