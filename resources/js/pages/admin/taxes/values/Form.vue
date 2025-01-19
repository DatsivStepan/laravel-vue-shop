<template>
    <div>
        <component-form v-bind:model="model" v-bind:config="config" v-on:add-validation-errors="addValidationErrors">
            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.value">
                        <input type="text" v-model="model.value" class="form-control" required
                               v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('value') && fieldsConfig.value.errors }"
                               id="value"/>
                    </form-field>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.tax">
                        <input
                            type="number"
                            step="0.01"
                            v-model="model.tax"
                            class="form-control"
                            required
                            v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('tax') && fieldsConfig.tax.errors }"
                            id="tax"
                        />
                    </form-field>
                </div>
            </div>
        </component-form>
    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import api from "@/api";

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
                    name: 'admin-advance-taxes-values',
                    params: {id: this.model.field_id}
                },
                apiBaseUrl: api.urls.admin.taxes.values.store,
            },
            fieldsConfig: {},
        }
    },
    mounted() {
        this.fieldsConfig = {
            value: {label: this.translate('taxes.values.form.value'), for: 'value', errors: null, required: true},
            tax: {label: this.translate('taxes.values.form.tax'), for: 'tax', errors: null, required: true},
        }
    },
    methods: {
        addValidationErrors(errors) {
            if (errors) {
                for (let errorKey in errors) {
                    this.fieldsConfig[errorKey].errors = errors[errorKey];
                }
            }
        },
    }
}
</script>
