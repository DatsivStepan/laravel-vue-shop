<!--0=> 'Text',
1 => 'Select',
2 => 'Multi Select',
3 => 'Checkbox',
4 => 'Textarea',
5 => 'Date',
6 => 'Date time',-->
<template>
    <div class="additional-fields">
        <form-field v-for="(customField, index) in customFields" :key="index"
                    v-bind:config="getConfig(customField)">
            <div v-if="customField.type == '0'">
                <input
                    type="text"
                    v-model="customField.value"
                    :class="{'form-control': true, 'is-invalid': !!customField.errors }"
                    :id="customField.name">
            </div>

            <div v-if="customField.type == '1'">
                <div class="for-icon">
                    <div class="icon-holder">
                        <ion-icon name="chevron-down"></ion-icon>
                    </div>
                    <select
                        v-model="customField.value"
                        :id="customField.name"
                        :class="{'form-control': true, 'is-invalid': customField.errors }"
                    >
                        <option v-for="option in customField.options" v-bind:value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>
            </div>

            <div v-if="customField.type == '2'">
                <select
                    v-model="customField.value"
                    multiple
                    :id="customField.name"
                    :class="{'form-control': true, 'is-invalid': customField.errors }"
                >
                    {{ customField.options }}
                    <option v-for="option in customField.options" v-bind:value="option">
                        {{ option }}
                    </option>
                </select>
            </div>

            <div v-if="customField.type == '3'">
                <input
                    type="checkbox"
                    :id="customField.name"
                    :class="{'table-input': true, 'is-invalid': customField.errors }"
                    v-model="customField.value"
                >
                <label :for="customField.name"></label>
            </div>
            <div v-if="customField.type == '4'">
                <textarea
                    v-model="customField.value"
                    :class="{'form-control': true, 'is-invalid': customField.errors }"
                    id="customField.name"
                >
                </textarea>
            </div>

            <div v-if="customField.type == '5'">
                <flat-pickr v-model="customField.value" :config="{altFormat: 'F j, Y', altInput: true}"></flat-pickr>
            </div>

            <div v-if="customField.type == '6'">
                <flat-pickr v-model="customField.value" :config="{enableTime: true, altFormat: 'F j, Y H:i', altInput: true}"></flat-pickr>
            </div>

        </form-field>

    </div>
</template>

<script>
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import FormField from './FormField.vue';
import '@sass/component/admin-area.scss'

export default {
    components: {
        FormField,
        FlatPickr
    },
    props: ['customFields'],
    data: function () {
        return {
            fieldsConfig: [],
        }
    },
    methods: {
        getConfig(customField) {
            return {label: customField.title, for: customField.name, errors: customField.errors}
        }
    }
}
</script>
