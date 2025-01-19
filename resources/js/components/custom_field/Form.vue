<template>
    <div class="form-field-select">
        <component-form :model="model" :config="config" @add-validation-errors="addValidationErrors">

            <form-field :config="fieldsConfig.title">
                <input type="text" v-model="model.title" class="form-control"
                   :class="{'is-invalid': fieldsConfig.hasOwnProperty('title') && fieldsConfig.title.errors }" id="title">
            </form-field>

            <form-field :config="fieldsConfig.fieldtype">
                <div class="for-icon">
                    <div class="icon-holder">
                        <ion-icon name="chevron-down"></ion-icon>
                    </div>
                    <select v-model="model.field_type" class="form-control" id="parentid"
                        :class="{'is-invalid': fieldsConfig.hasOwnProperty('fieldtype') && fieldsConfig.fieldtype.errors }">
                        <option v-for="fieldType in fieldTypes" :value="fieldType.value">
                            {{ fieldType.text }}
                        </option>
                    </select>
                </div>
            </form-field>

            <form-field v-if="typesWithOptions.includes(model.field_type)" :config="fieldsConfig.options">
                <textarea v-model="model.options" :class="{'is-invalid': fieldsConfig.hasOwnProperty('options') && fieldsConfig.options.errors }"
                    class="form-control" id="shortdescription"></textarea>
            </form-field>

            <form-field :config="fieldsConfig.required">
                <div class="for-icon">
                    <div class="icon-holder">
                            <ion-icon name="chevron-down"></ion-icon>
                        </div>
                    <select v-model="model.required" class="form-control" id="required"
                        :class="{'is-invalid': fieldsConfig.hasOwnProperty('required') && fieldsConfig.required.errors }">
                        <option v-for="requiredOption in requiredOptions" :value="requiredOption.value">
                            {{ requiredOption.text }}
                        </option>
                    </select>
                </div>
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
    props: {
        model: {
            type: Object,
        },
        instanceType: {
            type: String,
        },
    },
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-custom-fields',
                    params: {}
                },
                apiBaseUrl: '/admin/custom-fields',
            },
            typesWithOptions: [],
            fieldTypes: [],
            requiredOptions: [],
            fieldsConfig: {}
        }
    },
    mounted() {
        let app = this;
        app.getOptions();
        app.fieldsConfig = {
            title: app.prepareFieldTemplate('title'),
            fieldtype: app.prepareFieldTemplate('field_type'),
            options: app.prepareFieldTemplate('options'),
            required: app.prepareFieldTemplate('required'),
        };

        app.config.redirectRoute.params.push = {instanceType: app.instanceType}
    },
    methods: {
        prepareFieldTemplate(field) {
            let app = this;
            return {
                label: app.translate('custom_fields.form.' + field),
                for: field,
                errors: null
            };
        },
        changeImage(files) {
            this.model[files.field] = files.files;
        },
        addValidationErrors(errors) {
            let app = this;
            if (errors) {
                for (let errorKey in errors) {
                    app.fieldsConfig[errorKey].errors = errors[errorKey];
                }
            }
        },
        getOptions() {
            let app = this;
            axios.get(app.config.apiBaseUrl + '/get-options/edit')
                .then(function (resp) {
                    app.fieldTypes = resp.data.fieldTypes;
                    app.requiredOptions = resp.data.requiredOptions;
                    app.typesWithOptions = resp.data.typesWithOptions;
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>