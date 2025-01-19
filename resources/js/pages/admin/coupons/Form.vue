<template>
    <div>
        <component-form v-bind:model="model" v-bind:config="config" v-on:add-validation-errors="addValidationErrors">
            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.code">
                        <input type="text" v-model="model.code" class="form-control"
                               v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('code') && fieldsConfig.code.errors }" id="code">
                    </form-field>
                </div>

                <div class="col">
                    <form-field v-bind:config="fieldsConfig.coupontype">
                        <input type="radio" id="percents" :value="0" v-model="model.type">
                        <label for="percents">{{translate('system.percents')}}</label>
                        <input type="radio" id="currency" :value="1" v-model="model.type">
                        <label for="currency">{{translate('system.currency')}}</label>

                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.timestart">
                        <flat-pickr v-model="model.timestart" :config="pickerConfig" id="start-time"></flat-pickr>
                    </form-field>
                </div>

                <div class="col">
                    <form-field v-bind:config="fieldsConfig.timeend">
                        <flat-pickr v-model="model.timeend" :config="pickerConfig" id="end-time"></flat-pickr>
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.discount">
                        <input
                            type="number"
                            v-model="model.discount" class="form-control"
                            min="0"
                            :class="{'is-invalid': fieldsConfig.hasOwnProperty('discount') && fieldsConfig.discount.errors }"
                            id="code-discount-input"
                        >
                    </form-field>
                </div>

                <div class="col">
                    <form-field v-bind:config="fieldsConfig.usedperuser">
                        <input
                            type="number"
                            min="0"
                            v-model="model.usedperuser"
                            :class="{'form-control': true, 'is-invalid': fieldsConfig.hasOwnProperty('usedperuser') && fieldsConfig.usedperuser.errors }"
                            id="code-usedperuser-input"
                        >
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.usedcount">
                        <input
                            type="number"
                            v-model="model.usedcount"
                            min="0"
                            :class="{'form-control': true, 'is-invalid': fieldsConfig.hasOwnProperty('usedcount') && fieldsConfig.usedcount.errors }"
                            id="code-usedcount-input"
                        >
                    </form-field>
                </div>

                <div class="col">
                    <form-field v-bind:config="fieldsConfig.status">
                        <input type="hidden" v-model="model.status">
                        <v-select
                            id="enrol_end"
                            class="style-chooser"
                            v-model="selectedVAl"
                            placeholder="Enrollment end"
                            :options="statusOptions"
                            value="value"
                            @input="select"
                            label="text">
                            <template v-slot:option="option">
                                {{ option.text }}
                            </template>
                        </v-select>
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
       FlatPickr,
    },
    props: ['model'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-coupons',
                    params: {}
                },
                apiBaseUrl: '/admin/coupons',
            },
            statusOptions: [],
            fieldsConfig: {},
            pickerConfig: {
                enableTime: true,
                altFormat: "m/d/Y, h:i K",
                time_24hr: false,
                altInput: true,
                nextArrow: '',
                prevArrow: ''
            },
            selectedVAl: null
        }
    },
    mounted() {
        let app = this;
        app.getOptions()
        app.fieldsConfig = {
            code: {label: app.translate('coupons.code'), for: 'code', errors: null},
            timestart: {label: app.translate('system.starttime'), for: 'timestart', errors: null},
            timeend: {label: app.translate('system.endtime'), for: 'timeend', errors: null},
            coupontype: {label: app.translate('coupons.coupontype'), for: 'coupontype', errors: null},
            discount: {label: app.translate('coupons.discountvalue'), for: 'discount', errors: null, required: true},
            usedperuser: {label: app.translate('coupons.usedperuser'), for: 'usedperuser', errors: null},
            usedcount: {label: app.translate('coupons.usedcount'), for: 'usedcount', errors: null},
            status: {label: app.translate('coupons.status'), for: 'status', errors: null},
        }
    },
    methods: {
        select(val){
            this.model.status = val.value
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
                    app.statusOptions = resp.data.statusOptions;
                    app.selectedVAl =  resp.data.statusOptions[app.model.status].text
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>
