<template>
    <div>

        <component-form :model="model" :config="config" @add-validation-errors="addValidationErrors">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ translate('products.form_section.general_information') }}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.name">
                                <input type="text" v-model="model.name" class="form-control"
                                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('name') && fieldsConfig.name.errors }"
                                       id="name">
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.id_number">
                                <input type="text" v-model="model.id_number" class="form-control"
                                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('id_number') && fieldsConfig.id_number.errors }"
                                       id="id_number">
                            </form-field>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.categories"
                                        v-if="selectOptions.hasOwnProperty('categories')">
                                <custom-multiselect @update="model.categories = $event;"
                                                    :searchData="{url: config.apiBaseUrl + '/get-options/categories'}"
                                                    :selectedItems="model.categories"
                                                    :config="{multiple: true, searchAjax: true}"
                                                    ref="multiselectCategories"></custom-multiselect>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.price">
                                <input type="number" v-model="model.price" class="form-control"
                                       min="0"
                                       step="1"
                                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('price') && fieldsConfig.price.errors }"
                                       id="price">
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{translate('products.form_section.options')}}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.timestart">
                                <flat-pickr v-model="model.time_start" :config="pickerConfig"></flat-pickr>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.timeend">
                                <flat-pickr v-model="model.time_end" :config="pickerConfig"></flat-pickr>
                            </form-field>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <form-field :config="fieldsConfig.visible">
                            <input type="hidden" v-model="model.visible">
                            <v-select
                                id="visible"
                                class="style-chooser"
                                v-model="selectedVAl"
                                placeholder="Select status"
                                :options="selectOptions.statusOptions"
                                value="value"
                                :class="{'is-invalid': fieldsConfig.hasOwnProperty('visible') && fieldsConfig.visible.errors }"
                                @input="select"
                                label="text">
                                <template v-slot:option="option">
                                    {{ option.text }}
                                </template>
                            </v-select>
                        </form-field>
                    </div>
                    <div class="col">
                        <form-field :config="fieldsConfig.enablesessions" v-if="getSettingValue('enable_sessions') == 1">
                            <toggle-button v-model="model.enable_sessions" id="enablesessions"
                                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('enablesessions') && fieldsConfig.enablesessions.errors }"/>
                        </form-field>
                    </div>
                </div>
                <div class="row" v-if="getSettingValue('enable_shipping') == 1">
                    <div class="col">
                        <form-field :config="fieldsConfig.enable_shipping">
                            <toggle-button v-model="model.enable_shipping" id="enable_shipping"
                                           :class="{'is-invalid': fieldsConfig.hasOwnProperty('enable_shipping') && fieldsConfig.enable_shipping.errors }"/>
                        </form-field>
                    </div>
                    <div class="col">
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.seats') }}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.enable_seats">
                                <toggle-button v-model="model.enable_seats" id="enable_seats"
                                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('enable_seats') && fieldsConfig.enable_seats.errors }"/>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.seats">
                                <input
                                    type="number"
                                    v-model="model.seats"
                                    class="form-control"
                                    :disabled="(model.enable_seats != 1) || (model.enable_sessions == 1)"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('seats') && fieldsConfig.seats.errors }"
                                    id="seats"
                                    min="0"
                                >
                            </form-field>
                        </div>
                    </div>

                    <div class="row" v-if="getSettingValue('enable_seats_vendors')">
                        <div class="col">
                            <form-field :config="fieldsConfig.enable_buy_ing_seats">
                                <toggle-button v-model="model.enable_buy_ing_seats" id="enable_buy_ing_seats"
                                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('enable_buy_ing_seats') && fieldsConfig.enable_buy_ing_seats.errors }"/>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.max_seats_per_user">
                                <input
                                    type="number"
                                    v-model="model.max_seats_per_user"
                                    class="form-control"
                                    :disabled="model.enable_buy_ing_seats != 1"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('max_seats_per_user') && fieldsConfig.max_seats_per_user.errors }"
                                    id="max_seats_per_user"
                                    min="0"
                                >
                            </form-field>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col" v-if="getSettingValue('enable_taxes') == 1">
                            <form-field :config="fieldsConfig.enable_tax" v-if="getSettingValue('enable_taxes')">
                                <toggle-button v-model="model.enable_tax" id="enable_tax"
                                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('enable_tax') && fieldsConfig.enable_tax.errors }"/>
                            </form-field>
                        </div>
                        <div class="col">
                            <!--<form-field :config="fieldsConfig.showitems">
                                <div class="for-icon">
                                    <div class="icon-holder">
                                        <ion-icon name="chevron-down"></ion-icon>
                                    </div>
                                        <select v-model="model.show_items" class="form-control" id="showitems"
                                            :class="{'is-invalid': fieldsConfig.hasOwnProperty('showitems') && fieldsConfig.showitems.errors }">
                                            <option v-for="showItem in selectOptions.showItemsOptions" :value="showItem.value">
                                                {{ showItem.text }}
                                            </option>
                                        </select>
                                </div>
                            </form-field> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.description') }}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.image">
                                <image-uploader :field="'image_src'"
                                                :images="Array.isArray(model.image_src) ? model.image_src : (model.image_src == null ? [] : [model.image_src])"
                                                @changeImage="changeImage($event)"></image-uploader>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.featured_image">
                                <image-uploader :field="'featured_image_src'"
                                                :images="Array.isArray(model.featured_image_src) ? model.featured_image_src : (model.featured_image_src == null ? [] : [model.featured_image_src])"
                                                @changeImage="changeImage($event)"></image-uploader>
                            </form-field>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.shortdescription">
                                <textarea v-model="model.short_description"
                                          :class="{'is-invalid': fieldsConfig.hasOwnProperty('shortdescription') && fieldsConfig.shortdescription.errors }"
                                          class="form-control short-description" id="shortdescription"></textarea>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.video">
                                <video-uploader :field="'video_src'"
                                                :videos="Array.isArray(model.video_src) ? model.video_src : (model.video_src == null ? [] : [model.video_src])"
                                                @update="model.video_src = $event;"></video-uploader>
                            </form-field>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <form-field :config="fieldsConfig.description">
                                <vue-editor v-model="model.description" id="description"></vue-editor>
                            </form-field>
                        </div>
                        <div class="col-6">
                            <form-field :config="fieldsConfig.checkoutinfo">
                                <vue-editor v-model="model.checkout_info" id="checkoutinfo"></vue-editor>
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.billing_type') }}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.billing_type">
                                <b-form-radio-group
                                    id="radio-group-1"
                                    v-model="model.billing_type"
                                    :options="billingTypeOptions"
                                    name="radio-options"
                                ></b-form-radio-group>
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;" v-if="model.billing_type === 'subscription'">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.subscription') }}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.recurring_period">
                                <input type="hidden" v-model="model.recurring_period">
                                <v-select
                                    id="recurring_period"
                                    class="style-chooser"
                                    v-model="selectedVAlRecPeriod"
                                    placeholder="Select status"
                                    :options="selectOptions.recurringPeriods"
                                    value="value"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('recurring_period') && fieldsConfig.recurring_period.errors }"
                                    @input="selectRecPeriod"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.billing_cycles">
                                <input type="number" v-model="model.billing_cycles" class="form-control"
                                       min="1"
                                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('billing_cycles') && fieldsConfig.billing_cycles.errors }"
                                       id="billing_cycles">
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.subscription_expiration_action">
                                <select v-model="model.subscription_expiration_action" class="form-control"
                                        id="subscription_expiration_action"
                                        :class="{'is-invalid': fieldsConfig.hasOwnProperty('subscription_expiration_action') && fieldsConfig.subscription_expiration_action.errors }">
                                    <option v-for="showItem in selectOptions.subscriptionActions"
                                            :value="showItem.value">
                                        {{ showItem.text }}
                                    </option>
                                </select>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.subscription_cancellation_action">
                                <select v-model="model.subscription_cancellation_action" class="form-control"
                                        id="subscription_cancellation_action"
                                        :class="{'is-invalid': fieldsConfig.hasOwnProperty('subscription_cancellation_action') && fieldsConfig.subscription_cancellation_action.errors }">
                                    <option v-for="showItem in selectOptions.subscriptionActions"
                                            :value="showItem.value">
                                        {{ showItem.text }}
                                    </option>
                                </select>
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top:20px;" v-if="model.billing_type === 'installment'">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.installment') }}</h5>
                    <div class="row">
                        <div class="col">
                            <form-field :config="fieldsConfig.installment_recurring_period">

                                <input type="hidden" v-model="model.installment.recurring_period">
                                <v-select
                                    id="installment_recurring_period"
                                    class="style-chooser"
                                    v-model="selectedVAlRecPeriodInstallment"
                                    placeholder="Select status"
                                    :options="selectOptions.recurringPeriods"
                                    value="value"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('installment_recurring_period') && fieldsConfig.installment_recurring_period.errors }"
                                    @input="selectRecPeriodInstallment"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.installment_billing_cycles">
                                <input type="number" v-model="model.installment.billing_cycles" class="form-control"
                                       min="1"
                                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('installment_billing_cycles') && fieldsConfig.installment_billing_cycles.errors }"
                                       id="installment_billing_cycles">
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.installment_fee">
                                <input
                                    type="number"
                                    v-model="model.installment.fee"
                                    :class="{'form-control': true, 'is-invalid': fieldsConfig.hasOwnProperty('installment_fee') && fieldsConfig.installment_fee.errors }"
                                    id="installment_fee"
                                    step="1"
                                >
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.installment_fee_type">
                                <b-form-radio-group
                                    id="fee-type-group"
                                    v-model="model.installment.fee_type"
                                    :options="feeTypeOptions"
                                    name="fee-type-options"
                                ></b-form-radio-group>
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;" v-if="customFields">
                <div class="card-body card-body-additional">
                    <h5>{{ translate('products.form_section.additional_fields') }}</h5>
                    <custom-form-field :customFields="customFields"></custom-form-field>
                </div>
            </div>

            <div class="card" style="margin-top:20px;" v-if="getSettingValue('enable_reviews') == 1">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.product_review') }}</h5>
                    <div class="row">
                        <div class="col-6">
                            <form-field :config="fieldsConfig.enable_reviews">
                                <toggle-button v-model="model.enable_reviews" id="enable_reviews"
                                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('enable_reviews') && fieldsConfig.enable_reviews.errors }"/>
                            </form-field>
                        </div>
                        <div class="col-6">
                            <form-field :config="fieldsConfig.enable_reviews_approval">
                                <toggle-button v-model="model.enable_reviews_approval" id="enable_reviews_approval"
                                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('enable_reviews_approval') && fieldsConfig.enable_reviews_approval.errors }"/>
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.enrollment_options') }}</h5>
                    <div class="row" v-if="selectOptions.enroll">
                        <div class="col">
                            <form-field :config="fieldsConfig.enrol_start">
                                <input type="hidden" v-model="model.enrol_start">
                                <v-select
                                    id="enrol_start"
                                    class="style-chooser"
                                    v-model="selectedVAlEnrol_start"
                                    placeholder="Enrollment start"
                                    :options="selectOptions.enroll.start_types"
                                    value="value"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('enrol_start') && fieldsConfig.enrol_start.errors }"
                                    @input="selectEnrol_start"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.enrol_on">
                                <!-- enabled when enrol start - Course start or Specific date -->
                                <input type="hidden" v-model="model.enrol_on">
                                <v-select
                                    id="enrol_on"
                                    class="style-chooser"
                                    v-model="selectedVAlEnrol_on"
                                    :disabled="(model.enrol_start != 2) && (model.enrol_start != 3)"
                                    placeholder="Enroll on"
                                    :options="selectOptions.enroll.on_types"
                                    value="value"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('enrol_on') && fieldsConfig.enrol_on.errors }"
                                    @input="selectEnrol_on"
                                    label="text">
                                    <template v-slot:option="option">
                                        {{ option.text }}
                                    </template>
                                </v-select>
                            </form-field>
                        </div>
                    </div>

                    <div class="row" v-if="selectOptions.enroll">
                        <div class="col">
                            <form-field :config="fieldsConfig.enrol_start_date">
                                <!-- enabled when enrol start - Specific date -->
                                <flat-pickr v-model="model.enrol_start_date" :disabled="(model.enrol_start != 3)" :config="pickerConfig"></flat-pickr>
                            </form-field>
                        </div>
                        <div class="col">
                            <form-field :config="fieldsConfig.enrol_end">
                                <input type="hidden" v-model="model.enrol_end">
                                <v-select
                                    id="enrol_end"
                                    class="style-chooser"
                                    v-model="selectedVAlEnrol_end"
                                    placeholder="Enrollment end"
                                    :options="selectOptions.enroll.end_types"
                                    value="value"
                                    :class="{'is-invalid': fieldsConfig.hasOwnProperty('enrol_end') && fieldsConfig.enrol_end.errors }"
                                    @input="selectEnrol_end"
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
                            <form-field :config="fieldsConfig.enrol_period">
                                <duration-input @update="model.enrol_period = $event;" :model="model.enrol_period"></duration-input>
                            </form-field>
                        </div>
                    </div>

                </div>
            </div>

            <!--<div class="card rounded-0" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.instructors') }}</h5>

                    <form-field :config="fieldsConfig.instructors" v-if="selectOptions.hasOwnProperty('instructors')">

                        <custom-multiselect @update="model.instructors = $event;"
                                            :searchData="{url: config.apiBaseUrl + '/get-options/instructors'}"
                                            :selectedItems="model.instructors"
                                            :config="{multiple: true, searchAjax: true}"
                                            ref="multiselectInstructors"></custom-multiselect>

                    </form-field>
                </div>
            </div> -->

            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>{{ translate('products.form_section.theme') }}</h5>

                    <div class="row">
                        <form-field :config="fieldsConfig.theme_key" class="col-6">
                            <v-select
                                id="theme_key"
                                class="style-chooser"
                                v-model="model.theme_key"
                                placeholder="Select theme"
                                :options="selectOptions.themes"
                                value="value"
                                :class="{'is-invalid': fieldsConfig.hasOwnProperty('theme_key') && fieldsConfig.theme_key.errors }"
                                label="text"
                               >
                                <template v-slot:option="option">
                                    {{ option.text }}
                                </template>
                            </v-select>
                        </form-field>
                    </div>
                </div>
            </div>
        </component-form>
        <go-top :size="40" :bg-color="'#FFB800'" :boundary="300" :has-outline="false"></go-top>
    </div>
</template>
<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import CustomFormField from '@component/form/CustomFormField.vue';
import {VueEditor} from "vue2-editor";
import ImageUploader from '@component/form/ImageUploader.vue';
import VideoUploader from '@component/form/VideoUploader.vue';
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import CustomMultiselect from '@component/form/CustomMultiselect.vue';
import {ToggleButton} from 'vue-js-toggle-button';
import DurationInput from '@component/form/DurationInput.vue';
import GoTop from '@inotom/vue-go-top';

export default {
    components: {
        DurationInput,
        ComponentForm,
        FormField,
        VueEditor,
        ImageUploader,
        VideoUploader,
        FlatPickr,
        CustomFormField,
        CustomMultiselect,
        ToggleButton,
        GoTop,
    },
    props:{
        model: {
            type: Object,
        },
    },
    data: function () {
        return {
            config: {
                redirectRoute: {
                    name: 'admin-products',
                    params: {}
                },
                apiBaseUrl: '/admin/products',
            },
            selectOptions: {},
            fieldsConfig: {},
            pickerConfig: {
                enableTime: true,
                altFormat: "m/d/Y, h:i K",
                dateFormat: "Y-m-d",
                time_24hr: false,
                altInput: true,
                nextArrow: '',
                prevArrow: ''
            },
            feeTypeOptions: [
                {text: this.translate('products.installment.fee_type.percent'), value: 0},
                {text: this.translate('products.installment.fee_type.currency'), value: 1},
            ],
            selectedVAl: null,
            selectedVAlRecPeriod: null,
            selectedVAlRecPeriodInstallment: null,
            selectedVAlEnrol_start: null,
            selectedVAlEnrol_on: null,
            selectedVAlEnrol_end: null

        }
    },
    mounted() {
        let app = this;
        app.getOptions()
        app.fieldsConfig = {
            name: app.prepareFieldTemplate('name'),
            id_number: app.prepareFieldTemplate('id_number'),
            categories: app.prepareFieldTemplate('categories'),
            price: app.prepareFieldTemplate('price'),
            visible: app.prepareFieldTemplate('visible'),
            timestart: app.prepareFieldTemplate('timestart'),
            timeend: app.prepareFieldTemplate('timeend'),
            enable_seats: app.prepareFieldTemplate('enable_seats'),
            enable_shipping: app.prepareFieldTemplate('enable_shipping'),
            enable_tax: app.prepareFieldTemplate('enable_tax'),
            seats: app.prepareFieldTemplate('seats'),
            enablesessions: app.prepareFieldTemplate('enablesessions'),
            showitems: app.prepareFieldTemplate('showitems'),
            image: app.prepareFieldTemplate('image'),
            video: app.prepareFieldTemplate('video'),
            featured_image: app.prepareFieldTemplate('featured_image'),
            description: app.prepareFieldTemplate('description'),
            shortdescription: app.prepareFieldTemplate('shortdescription'),
            checkoutinfo: app.prepareFieldTemplate('checkoutinfo'),
            billing_cycles: app.prepareFieldTemplate('billing_cycles'),
            recurring_period: app.prepareFieldTemplate('recurring_period'),
            subscription_expiration_action: app.prepareFieldTemplate('subscription_expiration_action'),
            subscription_cancellation_action: app.prepareFieldTemplate('subscription_cancellation_action'),
            // instructors: app.prepareFieldTemplate('instructors'),
            enable_buy_ing_seats: app.prepareFieldTemplate('enable_buy_ing_seats'),
            max_seats_per_user: app.prepareFieldTemplate('max_seats_per_user'),
            theme_key: app.prepareFieldTemplate('theme_key'),
            installment_recurring_period: app.prepareFieldTemplate('installment_recurring_period'),
            installment_billing_cycles: app.prepareFieldTemplate('installment_billing_cycles'),
            installment_fee: app.prepareFieldTemplate('installment_fee'),
            installment_fee_type: app.prepareFieldTemplate('installment_fee_type'),
            billing_type: app.prepareFieldTemplate('billing_type', false),
            enrol_start: app.prepareFieldTemplate('enrol_start'),
            enrol_on: app.prepareFieldTemplate('enrol_on'),
            enrol_start_date: app.prepareFieldTemplate('enrol_start_date'),
            enrol_end: app.prepareFieldTemplate('enrol_end'),
            enrol_period: app.prepareFieldTemplate('enrol_period'),
            enable_reviews: app.prepareFieldTemplate('enable_reviews'),
            enable_reviews_approval: app.prepareFieldTemplate('enable_reviews_approval'),
        }
    },
    computed: {
        customFields() {
            return this.model.customFields;
        },
        billingTypeOptions() {
            let billingTypeOptions = [
                {text: this.translate('products.billing_type.regular'), value: 'regular'}
            ];

            if (this.getSettingValue('enable_subscription') == 1) {
                billingTypeOptions.push({
                    text: this.translate('products.billing_type.subscription'),
                    value: 'subscription'
                });
            }

            if (this.getSettingValue('enable_installment') == 1) {
                billingTypeOptions.push({
                    text: this.translate('products.billing_type.installment'),
                    value: 'installment'
                });
            }

            return billingTypeOptions;
        },
    },
    methods: {
        select(val){
            this.model.visible = val.value
        },
        selectRecPeriod(val){
            this.model.recurring_period = val.value
        },
        selectRecPeriodInstallment(val){
            this.model.installment.recurring_period = val.value
        },
        selectEnrol_start(val){
            this.model.enrol_start = val.value
        },
        selectEnrol_on(val){
            this.model.enrol_on = val.value
        },
        selectEnrol_end(val){
            this.model.enrol_end = val.value
        },
        prepareFieldTemplate(field, label = true) {
            let app = this;
            return {
                label: label ? app.translate('products.form.' + field) : '',
                for: field,
                errors: null
            };
        },
        changeImage(files) {
            this.model[files.field] = files.files;
        },
        addValidationErrors(errors) {
            let app = this;
            for (let fieldsConfigKey in app.fieldsConfig) {
                if(app.fieldsConfig[fieldsConfigKey]){
                    app.fieldsConfig[fieldsConfigKey].errors = null
                }
            }
            if (errors) {
                for (let errorKey in errors) {
                    if (errorKey == 'customFields') {
                        let customFieldsErorrs = errors[errorKey];
                        for (let cIndex in app.model.customFields) {
                            app.customFields[cIndex].errors = null;
                        }

                        for (let cfeError in customFieldsErorrs) {
                            let customFieldError = customFieldsErorrs[cfeError];
                            for (let cField in customFieldError) {
                                let $index = app.customFields.findIndex((element) => element.name == cField)
                                app.customFields[$index].errors = [customFieldError[cField]];
                                app.$set(app.customFields, $index, app.customFields[$index]);
                            }
                        }
                    } else {
                        if (app.fieldsConfig[errorKey]) {
                            app.fieldsConfig[errorKey].errors = errors[errorKey];
                        }
                    }
                }
            }
        },
        getOptions() {
            let app = this;
            axios.get(app.config.apiBaseUrl + '/get-options/edit')
                .then(function (resp) {
                    app.selectOptions = {
                        statusOptions: resp.data.statusOptions,
                        showItemsOptions: resp.data.showItemsOptions,
                        recurringPeriods: resp.data.recurringPeriods,
                        categories: resp.data.categories,
                        subscriptionActions: resp.data.subscriptionActions,
                        // instructors: resp.data.instructors,
                        themes: resp.data.themes,
                        enroll: resp.data.enroll,
                    }
                    app.selectedVAl = resp.data.statusOptions[app.model.visible].text
                    app.selectedVAlRecPeriod = resp.data.recurringPeriods[app.model.recurring_period].text
                    app.selectedVAlRecPeriodInstallment = resp.data.recurringPeriods[app.model.installment.recurring_period].text
                    app.selectedVAlEnrol_start = resp.data.enroll.start_types[app.model.enrol_start].text
                    app.selectedVAlEnrol_on = resp.data.enroll.on_types[app.model.enrol_on].text
                    app.selectedVAlEnrol_end = resp.data.enroll.end_types[app.model.enrol_end].text
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },
    }
}
</script>
