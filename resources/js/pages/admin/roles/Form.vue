<template>
    <div>

        <component-form :model="model" :config="config" @add-validation-errors="addValidationErrors">

            <form-field :config="fieldsConfig.name">
                <input type="text" v-model="model.name" class="form-control" 
                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('name') && fieldsConfig.name.errors }" id="name">
            </form-field>

        </component-form>

    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';

export default {
    components: {
       ComponentForm,
       FormField,
    },
    props: ['model'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-roles',
                    params: {}
                },
                apiBaseUrl: '/admin/roles',
            },
            fieldsConfig: {},
        }
    },
    mounted() {
        let app = this;
        app.fieldsConfig = {
            name: {label: app.translate('roles.form.name'), for: 'name', errors: null},
        }
    },
    methods: {
        addValidationErrors(errors) {
            let app = this;
            if (errors) {
                for (let errorKey in errors) {
                    app.fieldsConfig[errorKey].errors = errors[errorKey];
                }
            }
        },
    }
}
</script>
