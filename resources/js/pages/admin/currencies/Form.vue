<template>
    <div>
        <component-form :model="model" :config="config" @add-validation-errors="addValidationErrors">
            <form-field :config="fieldsConfig.code">
                <div v-if="mode === 'edit'">
                    <input type="hidden" v-model="model.code">
                    <v-select
                        id="code"
                        class="style-chooser"
                        v-model="selectedVAlCode"
                        placeholder="Select type"
                        :options="editCode"
                        value="value"
                        label="name"
                        disabled >
                        <template v-slot:option="option">
                            {{ option.name }}
                        </template>
                    </v-select>
                </div>
                <div v-else>
                    <input type="hidden" v-model="selectedCurrency">
                    <v-select
                        id="code-currency"
                        class="style-chooser"
                        v-model="selectedVAlCurrency"
                        placeholder="Select currency"
                        :options="currencies"
                        value="value"
                        @input="selectCurrency"
                        label="name"
                    >
                        <template v-slot:option="option">
                            {{ option.name }}
                        </template>
                    </v-select>
                </div>
            </form-field>

            <form-field :config="fieldsConfig.exchange_rate">
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    v-model="model.exchange_rate"
                    class="form-control"
                />
            </form-field>

            <form-field :config="fieldsConfig.format">
                <input type="text" v-model="model.format" class="form-control"/>
            </form-field>
            <div class="form-isdefault">
                <form-field :config="fieldsConfig.is_default">
                    <input type="checkbox" id="isdef" true-value="1" v-model="model.is_default" class="table-input">
                    <label for="isdef"></label>
                </form-field>
            </div>
        </component-form>

    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import '@sass/component/table.scss';
import * as qs from "qs";

export default {
    components: {
        ComponentForm,
        FormField,
        FlatPickr
    },
    props: ['model', 'mode'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-currencies',
                    params: {}
                },
                apiBaseUrl: '/admin/currencies',
            },
            fieldsConfig: {},
            currencies: [],
            selectedCurrency: null,
            selectedVAlCode: null,
            selectedVAlCurrency: null,
            editCode: []

        }
    },
    mounted() {
        let app = this;
        if (app.mode === 'add') {
            app.getAvailableCurrencies()
        }
        app.fieldsConfig = {
            code: {label: app.translate('currency.form.currency'), for: 'code', errors: null, required: true},
            exchange_rate: {label: app.translate('currency.form.exchange_rate'), for: 'exchange_rate', errors: null, required: true},
            format: {label: app.translate('currency.form.format'), for: 'format', errors: null, required: true},
            is_default: {label: app.translate('currency.form.is_default'), for: 'is_default', errors: null, required: false},
        }
        app.editCode = [{name: app.model.name, value: app.model.code }]
        app.selectedVAlCode = app.model.name
    },
    watch: {
        selectedCurrency(currency) {
            this.model.code = currency.code;
            this.model.format = currency.format;
            this.model.exchange_rate = currency.exchange_rate;
        }
    },
    methods: {
        selectCurrency(val){
            this.selectedCurrency = val
        },
        addValidationErrors(errors) {
            let app = this;
            if (errors) {
                for (let errorKey in errors) {
                    app.fieldsConfig[errorKey].errors = errors[errorKey];
                }
            }
        },
        getAvailableCurrencies() {
            let app = this;

            let params = {};

            if (this.mode === 'add') {
                params = {
                    filter: {active: false}
                }
            }
            axios.get(app.config.apiBaseUrl + '/select', {
                params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }
            })
                .then(function (resp) {
                    app.currencies = resp.data;
                    app.selectedVAlCurrency = app.selectedCurrency

                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        }
    }
}
</script>
