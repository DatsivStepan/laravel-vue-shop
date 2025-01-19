<template>
    <div>
        <component-form v-bind:model="data" v-bind:config="config" v-on:add-validation-errors="addValidationErrors">
            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.key">
                        <select v-model="data" class="form-control" required id="key">
                            <option v-for="field in fields" v-bind:value="field">
                                {{ field.name }}
                            </option>
                        </select>
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
            data: this.model,
            config: {
                redirectRoute: {name: 'admin-advance-taxes', params: {active_tab: 'taxes'}},
                apiBaseUrl: api.urls.admin.taxes.fields.store,
            },
            fields: [],
            fieldsConfig: {},
        }
    },
    mounted() {
        this.getOptions()
        this.fieldsConfig = {
            key: {label: this.translate('taxes.fields.form.key'), for: 'key', errors: null, required: true},
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
        getOptions() {
            let app = this;
            axios.get(app.config.apiBaseUrl + '/get-options')
                .then(function (resp) {
                    app.fields = resp.data;
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>
