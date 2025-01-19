<template>
    <div>
        <form v-on:submit="saveForm()">

            <div class="row" v-if="mSelect">
                <div class="col">
                    <label for="users">{{translate('vendors.form_users.select_users')}}:</label>
                    <custom-multiselect @update="assign.user_id = $event;" :searchData="mSelect.user" :selectedItems="assign.user_id"
                        :config="{multiple: true, searchAjax: true}" :placeholder="translate('system.search')" ref="multiselect"></custom-multiselect>
                </div>

                <div class="col">
                    <label for="types">{{translate('vendors.form_users.select_types')}}:</label>
                    <custom-multiselect v-if='types' @update="assign.type = $event;" :items='types' :selectedItems="assign.type"
                        :config="{multiple: false, searchAjax: false, closeOnSelect: true}" ref="multiselect_type"></custom-multiselect>
                </div>
            </div>

            <button class="btn btn-primary float-right mb-2 assign-btn-user mt">{{translate('vendors.form_users.assign')}}</button>
        </form>
    </div>
</template>

<script>
import CustomMultiselect from '@component/form/CustomMultiselect.vue';
export default {
    components: {
        CustomMultiselect
    },
    props: ['vendor'],
    data: function () {
        return {
            assign: {
                vendor_id: 0,
                user_id: [],
                type: 0
            },
            types: null
        }
    },
    computed: {
        mSelect: function() {
            let app = this;
            return {
                user: {
                    url: '/admin/vendors-users/get-users',
                    params: {
                        vendor_id: app.vendor.id,
                        type: app.assign.type,
                    }
                }
            }
        }
    },
    mounted() {
        this.getTupes();
    },
    methods: {
        clearForm() {
            let app = this;
            app.$refs.multiselect.clearAll();
        },
        getTupes() {
            let app = this;
            axios.get('/admin/vendors-users/get-types').then(response => {
                app.types = response.data;
            }).catch(function (resp) {
                console.error(resp);
            });
        },
        saveForm() {
            event.preventDefault();
            let app = this;
            app.assign.vendor_id = app.$route.params.id;
            app.$emit('save-form', app.assign);
        }
    }
}
</script>
