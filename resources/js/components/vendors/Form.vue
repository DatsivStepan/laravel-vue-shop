<template>
    <div>

        <component-form :model="model" :config="config" @add-validation-errors="addValidationErrors">

            <div class="row">
                <div class="col">
                    <form-field :config="fieldsConfig.name">
                        <input type="text" v-model="model.name" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('name') && fieldsConfig.name.errors }" id="name">
                    </form-field>
                </div>
                <div class="col">
                    <form-field :config="fieldsConfig.idnumber">
                        <input type="text" v-model="model.idnumber" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('idnumber') && fieldsConfig.idnumber.errors }" id="idnumber">
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field :config="fieldsConfig.email">
                        <input type="text" v-model="model.email" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('email') && fieldsConfig.email.errors }" id="email">
                    </form-field>
                </div>
                <div class="col">
                    <form-field :config="fieldsConfig.company">
                        <input type="text" v-model="model.company" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('company') && fieldsConfig.company.errors }" id="company">
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field :config="fieldsConfig.url">
                        <input type="text" v-model="model.url" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('url') && fieldsConfig.url.errors }" id="url">
                    </form-field>
                </div>
                <div class="col">
                    <form-field :config="fieldsConfig.type">

                        <input type="hidden" v-model="model.type">
                        <v-select
                            id="type"
                            class="style-chooser"
                            v-model="selectedVAlType"
                            placeholder="Select type"
                            :options="typesOptions"
                            value="value"
                            :class="{'is-invalid': fieldsConfig.hasOwnProperty('type') && fieldsConfig.type.errors }"
                            @input="selectType"
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
                    <form-field :config="fieldsConfig.image">
                        <image-uploader :field="'image_src'" :images="Array.isArray(model.image_src) ? model.image_src : (model.image_src == null ? [] : [model.image_src])"
                            @changeImage="changeImage($event)"></image-uploader>
                    </form-field>
                </div>
            </div>


        </component-form>

    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import ImageUploader from '@component/form/ImageUploader.vue';

export default {
    components: {
       ComponentForm,
       FormField,
       ImageUploader
    },
    props: ['model'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-vendors',
                    params: {}
                },
                apiBaseUrl: '/admin/vendors',
            },
            typesOptions: [],
            fieldsConfig: {},
            selectedVAlType: null

        }
    },
    mounted() {
        let app = this;
        app.getOptions()
        app.fieldsConfig = {
            name: app.prepareFieldTemplate('name', true),
            idnumber: app.prepareFieldTemplate('idnumber'),
            type: app.prepareFieldTemplate('type'),
            email: app.prepareFieldTemplate('email'),
            company: app.prepareFieldTemplate('company'),
            url: app.prepareFieldTemplate('url'),
            image: app.prepareFieldTemplate('image'),
        }
    },
    methods: {
        selectType(val){
            this.model.type = val.value
        },
        prepareFieldTemplate(field, required = false) {
            let app = this;
            return {
                label: app.translate('vendors.form.' + field),
                for: field,
                errors: null,
                required: required
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
                    app.typesOptions = resp.data.typesOptions;
                    app.selectedVAlType = resp.data.typesOptions[app.model.type].text;
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>
