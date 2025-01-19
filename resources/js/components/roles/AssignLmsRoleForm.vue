<template>
    <div>
        <form v-on:submit="saveForm()">

            <div class="row" v-if='fieldsConfig'>
                <div class="col">
                    <form-field :config="fieldsConfig.lms_role_id">
                        <input type="number" v-model="assign.lms_role_id" class="form-control"
                            :class="{'is-invalid': fieldsConfig.hasOwnProperty('lms_role_id') && fieldsConfig.lms_role_id.errors }" id="lms_role_id">
                    </form-field>
                </div>
                <div class="col">
                    <form-field :config="fieldsConfig.lms_role_name">
                        <input type="text" v-model="assign.lms_role_name" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('lms_role_name') && fieldsConfig.lms_role_name.errors }" id="lms_role_name">
                    </form-field>
                </div>
            </div>

            <button class="btn btn-primary float-right mb-2 assign-form-btn">{{translate('courses.assign')}}</button>
        </form>
    </div>
</template>

<script>
import FormField from '@component/form/FormField.vue';

export default {
    props: ['role'],
    components: {
       FormField
    },
    data: function () {
        let app = this;
        return {
            assign: {
                role_id: 0,
                lms_role_id: 0,
                lms_role_name: ''
            },
            fieldsConfig: null
        }
    },
    mounted() {
        let app = this;
        app.fieldsConfig = {
            lms_role_id: app.prepareFieldTemplate('lms_role_id', false),
            lms_role_name: app.prepareFieldTemplate('lms_role_name', true)
        }
    },
    methods: {
        prepareFieldTemplate(field, required) {
            let app = this;
            return {
                label: app.translate('roles.lms_assign.form.' + field),
                for: field,
                errors: null,
                required: required
            };
        },
        clearForm() {
            let app = this;
            app.assign.lms_role_id = 0;
            app.assign.lms_role_name = '';
        },
        saveForm() {
            event.preventDefault();
            let app = this;
            app.assign.role_id = app.role.id;

            axios.post('/admin/roles-lms', app.assign)
                .then(function (resp) {
                    app.clearForm();
                    app.$emit('get-models');
            })
            .catch(function (resp) {
               console.error(resp);
            });
        }
    }
}
</script>
