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
                    <form-field :config="fieldsConfig.id_number">
                        <input type="text" v-model="model.id_number" class="form-control"
                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('id_number') && fieldsConfig.id_number.errors }" id="id_number">
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <form-field :config="fieldsConfig.parentId">

                        <custom-multiselect v-if="options.parentOptions.length" @update="model.parent_id = $event;" :config="{multiple: false, searchAjax: false}"
                            :selectedItems="model.parent_id" :items="options.parentOptions" ref="multiselect"></custom-multiselect>

                    </form-field>
                </div>
                <div class="col">
                    <form-field :config="fieldsConfig.visible">
                        <input type="hidden" v-model="model.visible">
                        <v-select
                            id="visible"
                            class="style-chooser"
                            v-model="selectedVAl"
                            placeholder="Select status"
                            :options="options.statusOptions"
                            value="value"
                            :class="{'is-invalid': fieldsConfig.hasOwnProperty('visible') && fieldsConfig.visible.errors }"
                            @input="select"
                            label="text">
                            <template v-slot:option="option">
                                {{ option.text }}
                            </template>
                        </v-select>
<!--                        <select v-model="model.visible" class="form-control" id="visible"-->
<!--                            :class="{'is-invalid': fieldsConfig.hasOwnProperty('visible') && fieldsConfig.visible.errors }">-->
<!--                            <option v-for="statusOption in options.statusOptions" :value="statusOption.value">-->
<!--                                {{ statusOption.text }}-->
<!--                            </option>-->
<!--                        </select>-->
                    </form-field>
                </div>
            </div>

            <div class="row">
                <div class="col w-50 create-uploader">
                    <form-field :config="fieldsConfig.image">
                        <image-uploader :field="'image_src'" :images="Array.isArray(model.image_src) ? model.image_src : (model.image_src == null ? [] : [model.image_src])"
                            @changeImage="changeImage($event)"></image-uploader>
                    </form-field>
                </div>
                <div class="col w-50 create-uploader">
                    <form-field :config="fieldsConfig.description">
                        <vue-editor v-model="model.description" id="description"></vue-editor>
                    </form-field>
                </div>
            </div>

        </component-form>

    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import { VueEditor } from "vue2-editor";
import ImageUploader from '@component/form/ImageUploader.vue';
import CustomMultiselect from '@component/form/CustomMultiselect.vue';

export default {
    components: {
       ComponentForm,
       FormField,
       VueEditor,
       ImageUploader,
       CustomMultiselect
    },
    props: ['model'],
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-categories',
                    params: {}
                },
                apiBaseUrl: '/admin/categories',
            },
            options: {
                statusOptions: [],
                parentOptions: []
            },
            fieldsConfig: {},
            selectedVAl: null
        }
    },
    mounted() {
        let app = this;
        app.getOptions();
        app.fieldsConfig = {
            name: app.prepareFieldTemplate('name', true),
            id_number: app.prepareFieldTemplate('idnumber', false),
            parentId: app.prepareFieldTemplate('parentid', false),
            visible: app.prepareFieldTemplate('visible', false),
            image: app.prepareFieldTemplate('image', false),
            description: app.prepareFieldTemplate('description', false)
        }
    },
    methods: {
        select(val){
            this.model.visible = val.value
        },
        prepareFieldTemplate(field, required) {
            let app = this;
            return {
                label: app.translate('categories.' + field),
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
                    let data = resp.data;
                    if (data) {
                        app.options.statusOptions = data.statusOptions;
                        app.options.parentOptions = data.parentOptions;
                        app.selectedVAl =  app.options.statusOptions[app.model.visible].text
                    }
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>
