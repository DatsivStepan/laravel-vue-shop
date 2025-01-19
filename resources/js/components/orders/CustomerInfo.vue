<template>

    <div class="data-page-info">
        <form  @submit.prevent="saveForm">
            <div class="row">
                <div class="col-12 col-md-6">
                    <form-field :config="fieldsConfig.first_name">
                        <input type="text" v-model="customer.first_name" class="form-control"
                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('first_name') && fieldsConfig.first_name.errors }" id="first_name">
                    </form-field>
                </div>
                <div class="col-12 col-md-6">
                    <form-field :config="fieldsConfig.last_name">
                        <input type="text" v-model="customer.last_name" class="form-control"
                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('last_name') && fieldsConfig.last_name.errors }" id="first_name">
                    </form-field>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <form-field :config="fieldsConfig.street_address">
                        <input type="text" v-model="customer.street_address" class="form-control"
                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('street_address') && fieldsConfig.street_address.errors }" id="first_name">
                    </form-field>
                </div>
                <div class="col-12 col-md-6">
                    <form-field :config="fieldsConfig.city">
                        <input type="text" v-model="customer.city" class="form-control"
                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('city') && fieldsConfig.city.errors }" id="first_name">
                    </form-field>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <form-field :config="fieldsConfig.country">
                        <input type="text" v-model="customer.country" class="form-control"
                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('country') && fieldsConfig.country.errors }" id="first_name">
                    </form-field>
                </div>
                <div class="col-12 col-md-6">
                    <form-field :config="fieldsConfig.zip">
                        <input type="text" v-model="customer.zip" class="form-control"
                               :class="{'is-invalid': fieldsConfig.hasOwnProperty('zip') && fieldsConfig.zip.errors }" id="first_name">
                    </form-field>
                </div>
            </div>
            <form-field :config="fieldsConfig.apartment">
                <input type="text" v-model="customer.apartment" class="form-control"
                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('apartment') && fieldsConfig.apartment.errors }" id="first_name">
            </form-field>
            <form-field :config="fieldsConfig.email">
                <input type="email" v-model="customer.email" class="form-control"
                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('email') && fieldsConfig.email.errors }" id="first_name">
            </form-field>
            <form-field :config="fieldsConfig.phone">
                <input type="text" v-model="customer.phone" class="form-control"
                       :class="{'is-invalid': fieldsConfig.hasOwnProperty('phone') && fieldsConfig.phone.errors }" id="first_name">
            </form-field>

            <div v-if='tabs' class="buttons-checkout-options">
                <checkout-pagination :tabs="tabs" :in="'customer-info'" @plus-tab="plusTab"
                    @minus-tab="minusTab" ref="pagination"></checkout-pagination>
            </div>
        </form>
    </div>

</template>

<script>
import ComponentForm from '@component/form/Form.vue';
import FormField from '@component/form/FormField.vue';
import CheckoutPagination from './Pagination.vue';

export default {
    components: {
       ComponentForm,
       FormField,
       CheckoutPagination
    },
    props: ['order', 'tabs'],
    data: function () {
        return {
            customer: {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                street_address: '',
                apartment: '',
                city: '',
                country: '',
                zip: '',
            },
            fieldsConfig: {}
       }
    },
    mounted() {
        let app = this;
        app.fieldsConfig = {
            first_name: app.prepareFieldTemplate('first_name'),
            last_name: app.prepareFieldTemplate('last_name'),
            email: app.prepareFieldTemplate('email'),
            phone: app.prepareFieldTemplate('phone'),
            street_address: app.prepareFieldTemplate('street_address'),
            apartment: app.prepareFieldTemplate('apartment'),
            city: app.prepareFieldTemplate('city'),
            country: app.prepareFieldTemplate('country'),
            zip: app.prepareFieldTemplate('zip'),
        }
        app.getConsumerData();
    },
    methods: {
        plusTab(toId) {
            let app = this;
            axios.patch('/admin/customer/' + app.customer.id, app.customer)
                .then(function (resp) {
                    app.$refs.pagination.toTab(toId);
                })
                .catch(error => {
                    if (error.response.status == 422){
                        app.addValidationErrors(error.response.data.errors);
                    }
                })
        },
        minusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        prepareFieldTemplate(field) {
            let app = this;
            return {
                label: app.translate('users.' + field),
                for: field,
                errors: null
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
        getConsumerData() {
            let app = this;

            let id = app.order.user_id;
            if (id) {
                axios.get('/admin/customer/' + id)
                    .then(function (resp) {
                        app.customer = resp.data.data;
                    })
                    .catch(function () {
                        console.log("error get customer id:" + id);
                    });
            }
        },
     }
}
</script>
