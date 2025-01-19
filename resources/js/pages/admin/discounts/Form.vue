<template>
    <div>
        <component-form v-bind:model="model" v-bind:config="config" v-on:add-validation-errors="addValidationErrors">
            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.name">
                        <input type="text" v-model="model.name" class="form-control" required
                               v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('name') && fieldsConfig.name.errors }" id="name">
                    </form-field>
                </div>
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.time_start">
                        <flat-pickr v-model="model.time_start" :config="pickerConfig"></flat-pickr>
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.used_per_user">
                        <input
                            type="number"
                            v-model="model.used_per_user"
                            class="form-control"
                            min="0"
                        />
                    </form-field>
                </div>
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.time_end">
                        <flat-pickr v-model="model.time_end" :config="pickerConfig"></flat-pickr>
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.max_applied_products">
                        <input
                            type="number"
                            v-model="model.max_applied_products"
                            class="form-control"
                            min="0"
                        />
                    </form-field>
                </div>
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.discount">
                        <input
                            type="number"
                            min="0"
                            v-model="model.discount"
                            required
                            :class="{'form-control': true, 'is-invalid': fieldsConfig.hasOwnProperty('discount') && fieldsConfig.discount.errors }"
                            id="discount"
                        />
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.status">
                        <select v-model="model.status" class="form-control" id="status">
                            <option v-for="statusOption in statusOptions" v-bind:value="statusOption.value">
                                {{ statusOption.text }}
                            </option>
                        </select>
                    </form-field>
                </div>
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.type">
                        <select v-model="model.type" class="form-control" id="type">
                            <option v-for="typeOption in typeOptions" v-bind:value="typeOption.value">
                                {{ typeOption.text }}
                            </option>
                        </select>
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.min_number">
                        <input
                            type="number"
                            v-model="model.min_number"
                            class="form-control"
                            :disabled="model.type !== conditionType"
                            min="0"
                        />
                    </form-field>
                </div>
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.max_number">
                        <input
                            type="number"
                            min="0"
                            v-model="model.max_number"
                            class="form-control"
                            :disabled="model.type !== conditionType"
                        />
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.description">
                        <textarea v-model="model.description" class="form-control"/>
                    </form-field>
                </div>
            </div>
        </component-form>
    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
    components: {
       ComponentForm,
       FormField,
       FlatPickr
    },
    props: ['model'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-discounts',
                    params: {}
                },
                apiBaseUrl: '/admin/discounts',
            },
            statusOptions: [],
            typeOptions: [],
            fieldsConfig: {},
            pickerConfig: {
                enableTime: true,
                altFormat: "m/d/Y, h:i K",
                time_24hr: false,
                altInput: true
            },
            conditionType: 3,
        }
    },
    mounted() {
        let app = this;
        app.getOptions()
        app.fieldsConfig = {
            name: {label: app.translate('discounts.form.name'), for: 'code', errors: null, required: true},
            time_start: {label: app.translate('system.starttime'), for: 'times_tart', errors: null},
            time_end: {label: app.translate('system.endtime'), for: 'time_end', errors: null},
            type: {label: app.translate('discounts.form.type'), for: 'type', errors: null},
            discount: {label: app.translate('discounts.form.discount'), for: 'discount', errors: null, required: true},
            used_per_user: {label: app.translate('discounts.form.used_per_user'), for: 'used_per_user', errors: null},
            max_applied_products: {label: app.translate('discounts.form.max_applied_products'), for: 'max_applied_products', errors: null},
            status: {label: app.translate('discounts.form.status'), for: 'status', errors: null},
            min_number: {label: app.translate('discounts.form.min_number'), for: 'min_number', errors: null},
            max_number: {label: app.translate('discounts.form.max_number'), for: 'max_number', errors: null},
            description: {label: app.translate('discounts.form.description'), for: 'description', errors: null},
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
        getOptions() {
            let app = this;
            axios.get(app.config.apiBaseUrl + '/get-options/edit')
                .then(function (resp) {
                    app.statusOptions = resp.data.statusOptions;
                    app.typeOptions = resp.data.typeOptions;
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>
