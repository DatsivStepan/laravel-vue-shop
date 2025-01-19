<template>
    <div>
        <form @submit="saveForm()">
            <div class="form-group row">
                <div class="col-6">
                <label for="vendors" class="form-control-label">{{translate('vendors.select_vendors')}}:</label>
                    <custom-multiselect @update="vendorIds = $event;" :searchData="mSelect.searchData" :selectedItems="vendorIds"
                         :config="{multiple: true, searchAjax: true}" ref="multiselect"></custom-multiselect>
                </div>
            </div>

            <button class="btn btn-primary float-right mb-2 assign-btn">{{translate('vendors.assign')}}</button>
        </form>
    </div>
</template>

<script>
import CustomMultiselect from '@component/form/CustomMultiselect.vue';
export default {
    components: {
        CustomMultiselect
    },
    props: {
        instanceType: {
            type: Number
        },
        instanceId: {
            type: Number
        }
    },
    data: function () {
        return {
            vendorIds: [],
            mSelect: {
                searchData: {}
            }
        }
    },
    mounted() {
        let app = this;
        app.mSelect.searchData = {
            url: '/admin/vendors-assign/get-vendors',
            params: {
                instanceId: app.instanceId,
                instanceType: app.instanceType
            }
        }
    },
    methods: {
        saveForm() {
            event.preventDefault();

            let app = this;
            axios.post('/admin/vendors-assign/assign', {
                instanceId: app.instanceId,
                instanceType: app.instanceType,
                ids: app.vendorIds
            })
            .then(function (resp) {
                app.$emit('reload-data');
                app.$refs.multiselect.clearAll();
            })
            .catch(function (resp) {
               console.error(resp);
            });
        }
    }
}
</script>