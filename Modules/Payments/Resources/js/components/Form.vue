<template>
    <div>
        <component-form
            :model="model"
            :config="config"
            v-bind:fieldsConfig="fieldsConfig"
            @add-validation-errors="addValidationErrors"
        >
            <div class="card rounded-0">
                <div class="card-body">

                    <div class="row">
                        <div class="col">
                            <form-field v-bind:config="fieldsConfig.name">
                                <input type="text" v-model="model.name" class="form-control"
                                       v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('name') && fieldsConfig.name.errors }"
                                       id="name">
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field v-bind:config="fieldsConfig.currency">
                                <input type="hidden" v-model="model.currency">
                                <v-select
                                    id="currency"
                                    class="style-chooser"
                                    v-model="selectedValCurrency"
                                    :placeholder="translate('system.all')"
                                    :options="options.currencies"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('currency') && fieldsConfig.currency.errors }"
                                    value="value"
                                    @input="selectCurrency"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.type">
                                <input type="hidden" v-model="model.type">
                                <v-select
                                    id="type"
                                    class="style-chooser"
                                    v-model="selectedValtype"
                                    :placeholder="translate('system.pleaseselect')"
                                    :options="options.paymentTypes"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('type') && fieldsConfig.type.errors }"
                                    value="value"
                                    @input="selectType"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field v-bind:config="fieldsConfig.status">
                                <input type="hidden" v-model="model.status">
                                <v-select
                                    id="status"
                                    class="style-chooser"
                                    v-model="selectedValStatus"
                                    :placeholder="translate('system.pleaseselect')"
                                    :options="options.statuses"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('status') && fieldsConfig.status.errors }"
                                    value="value"
                                    @input="selectStatus"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                    </div>

                </div>
            </div>

            <div v-if="fieldsSettings" class="card rounded-0" style="margin-top: 20px;">
                <div class="card-body">

                    <div class="row">
                        <div v-for="(field, index) in fieldsSettings" class="col-sm-6">
                            <form-field v-if="field.type=='text'" :config="field">
                                <input type="text" class="form-control" :id="`settings_${ field.name }`"
                                       :name="`settings[${ field.name }]`" v-model="model.settings[field.name]">
                            </form-field>

                            <form-field v-if="field.type=='select'" :config="field">
                                <input type="hidden" v-model="model.settings[field.name]">
                                <v-select
                                    :id="`settings_${ field.name }`"
                                    class="style-chooser"
                                    v-model="selectedValSettings"
                                    :placeholder="translate('system.pleaseselect')"
                                    :options="field.values"
                                    value="value"
                                    @input="selectSettings"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>

                            </form-field>
                        </div>
                    </div>

                </div>
            </div>

        </component-form>

    </div>
</template>
<script>
import ComponentForm from './CustomForm.vue';
import FormField from './FormField.vue';

export default {
    components: {
        ComponentForm,
        FormField,
    },
    props: {
        model: {
            type: Object,
        },
    },
    data: function () {
        return {
            config: {
                redirectRoute: 'admin-payments',
                apiBaseUrl: '/admin/payments',
            },
            options: {
                paymentTypes: [],
                currencies: [],
                statuses: [],
            },
            fieldsConfig: {},
            fieldsSettings: null,
            selectedValCurrency: null,
            selectedValtype: null,
            selectedValStatus: null,
            selectedValSettings: null,
            fieldValues: []

        }
    },
    mounted() {
        let app = this;
        app.getOptions()
        app.fieldsConfig = {
            name: app.prepareFieldTemplate('name', true),
            currency: app.prepareFieldTemplate('currency', true),
            type: app.prepareFieldTemplate('type', true),
            status: app.prepareFieldTemplate('status')
        }
        app.getFieldsSettings(app.model.type);
        app.selectedValtype = app.model.type;

    },
    methods: {
        selectSettings(val){
            this.fieldsSettings.forEach(el => {
                if(el.type == 'select'){
                    this.model.settings[el.name] = val
                }
            })
        },
        selectStatus(val){
            this.model.status = val.value
        },
        selectType(val){
            this.model.type = val.value
            this.getFieldsSettings(this.model.type)
        },
        selectCurrency(val){
            this.model.currency = val.value
        },
        prepareFieldTemplate(field, required = false) {
            let app = this;
            return {
                label: app.translate('payments.form.' + field),
                for: field,
                errors: null,
                required: required
            };
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
                    app.options.paymentTypes = resp.data.paymentTypes;
                    app.options.currencies = resp.data.currencies;
                    app.options.statuses = resp.data.statuses;
                    app.selectedValCurrency = app.model.currency;
                    app.selectedValStatus =  resp.data.statuses[app.model.status].text;
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
        getFieldsSettings(type) {
            let app = this;
            if (!type) {
                app.fieldsSettings = null;
                return;
            }
            axios.get(app.config.apiBaseUrl + '/get-field-settings/' + type)
                .then(function (resp) {
                    app.fieldsSettings = resp.data;
                })
                .catch(function () {
                    console.error('Get Fields Settings Error');
                });
        },
    }
}
</script>
