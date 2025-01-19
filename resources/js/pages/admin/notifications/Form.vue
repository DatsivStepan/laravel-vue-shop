<template>
    <div>
        <component-form v-bind:model="model" v-bind:config="config" v-on:add-validation-errors="addValidationErrors">
            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.subject">
                        <input type="text" v-model="model.subject" class="form-control" required
                               v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('subject') && fieldsConfig.subject.errors }"
                               id="subject"/>
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
                            :options="statusOptions"
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

            <div class="row">
                <div class="col">
                    <form-field v-bind:config="fieldsConfig.body">
                        <vue-editor v-model="model.body" required
                                    v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('body') && fieldsConfig.body.errors }"
                                    id="body"></vue-editor>
                        <div>{{translate('notifications.tags')}}: <strong v-for="(tag, index) in model.tags" :key="index">{{tag}}</strong></div>
                    </form-field>
                </div>
            </div>
        </component-form>
    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import {VueEditor} from "vue2-editor";

export default {
    components: {
        ComponentForm,
        FormField,
        VueEditor
    },
    props: ['model'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-notifications',
                    params: {}
                },
                apiBaseUrl: '/notifications/admin/templates',
            },
            statusOptions: [],
            fieldsConfig: {},
            selectedValStatus: []

        }
    },
    mounted() {
        let app = this;
        app.fieldsConfig = {
            subject: {label: app.translate('notifications.form.subject'), for: 'subject', errors: null, required: true},
            status: {label: app.translate('discounts.form.status'), for: 'status', errors: null},
            body: {label: app.translate('notifications.form.body'), for: 'body', errors: null, required: true},
        }
        app.statusOptions = [
            {
                text: this.translate('system.active'),
                value: true
            },
            {
                text: this.translate('system.inactive'),
                value: false
            }
        ]
        app.statusOptions.forEach(el => {
            if(el.value === app.model.status){
                app.selectedValStatus = {text:el.text, value: el.value}
            }
        })

    },
    methods: {
        selectStatus(val){
            this.model.status = val.value
        },
        addValidationErrors(errors) {
            let app = this;
            if (errors) {
                for (let errorKey in errors) {
                    app.fieldsConfig[errorKey].errors = errors[errorKey];
                }
            }
        }
    }
}
</script>
