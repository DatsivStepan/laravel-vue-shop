<template>

    <div class="data-page-info">
        <form  @submit.prevent="saveForm">
            <div class="row">
                <div class="col-12 col-md-6">
                <form-field v-bind:config="fieldsConfig.name">
                    <input type="text" v-model="shipping.name" class="form-control"
                       v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('name') && fieldsConfig.name.errors }" id="name">
                </form-field>
                </div>
                <div class="col-12 col-md-6">
                <form-field v-bind:config="fieldsConfig.email">
                    <input type="email" v-model="shipping.email" class="form-control"
                       v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('email') && fieldsConfig.email.errors }" id="first_name">
                </form-field>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                <form-field v-bind:config="fieldsConfig.address">
                    <input type="text" v-model="shipping.address" class="form-control"
                       v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('phone') && fieldsConfig.address.errors }" id="first_name">
                </form-field>
                </div>
                <div class="col-12 col-md-6">
                <form-field v-bind:config="fieldsConfig.city">
                    <input type="text" v-model="shipping.city" class="form-control"
                       v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('city') && fieldsConfig.city.errors }" id="first_name">
                </form-field>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                <form-field v-bind:config="fieldsConfig.state">
                    <input type="text" v-model="shipping.state" class="form-control"
                       v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('state') && fieldsConfig.state.errors }" id="first_name">
                </form-field>
                </div>
                <div class="col-12 col-md-6">
                    <form-field v-bind:config="fieldsConfig.zip_code">
                        <input type="text" v-model="shipping.zip_code" class="form-control"
                   v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('zip_code') && fieldsConfig.zip_code.errors }" id="first_name">
                     </form-field>
                </div>
            </div>
            <form-field v-bind:config="fieldsConfig.phone">
                <input type="text" v-model="shipping.phone" class="form-control"
                   v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('phone') && fieldsConfig.phone.errors }" id="first_name">
            </form-field>

            <form-field v-bind:config="fieldsConfig.note">
                <textarea v-model="shipping.note"
                    v-bind:class="{'is-invalid': fieldsConfig.hasOwnProperty('note') && fieldsConfig.note.errors }"
                    class="form-control" id="shortdescription"></textarea>
            </form-field>

            <div v-if='tabs' class="buttons-checkout-options">
                <checkout-pagination v-bind:tabs="tabs" v-bind:in="'shipping'" v-on:plus-tab="plusTab"
                    v-on:minus-tab="minusTab" ref="pagination"></checkout-pagination>
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
        let app = this;
        return {
            shipping: {
                name: '',
                email: '',
                address: '',
                city: '',
                state: '',
                zip_code: '',
                phone: '',
                note: '',
                order_id: app.order.id
            },
            fieldsConfig: {}
        }
    },
    mounted() {
        let app = this;
        app.fieldsConfig = {
            name: app.prepareFieldTemplate('name'),
            email: app.prepareFieldTemplate('email'),
            address: app.prepareFieldTemplate('address'),
            city: app.prepareFieldTemplate('city'),
            state: app.prepareFieldTemplate('state'),
            zip_code: app.prepareFieldTemplate('zip_code'),
            phone: app.prepareFieldTemplate('phone'),
            note: app.prepareFieldTemplate('note')
        }

        app.getShippingData();
    },
    methods: {
        createShipping(toId) {
            let app = this;
            axios.post('/shipping', app.shipping)
                .then(function (resp) {
                    app.$refs.pagination.toTab(toId);
                })
                .catch(error => {
                    if (error.response.status == 422){
                        app.addValidationErrors(error.response.data.errors);
                    }
                });
        },
        updateShipping(toId) {
            let app = this;
            axios.patch('/shipping/' + app.shipping.id, app.shipping)
                .then(function (resp) {
                    app.$refs.pagination.toTab(toId);
                })
                .catch(error => {
                    if (error.response.status == 422){
                        app.addValidationErrors(error.response.data.errors);
                    }
                });
        },
        plusTab(toId) {
            let app = this;
            if (app.shipping.id) {
                app.updateShipping(toId);
            } else {
                app.createShipping(toId);
            }
        },
        minusTab(toId) {
            this.$refs.pagination.toTab(toId);
        },
        prepareFieldTemplate(field) {
            let app = this;
            return {
                label: app.translate('shipping.form.' + field),
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
        getShippingData() {
            let app = this;
            let params = {
                order_id: app.order.id,
                user_id: app.order.user_id,
            }

            axios.get('/shipping/get-shipping-data', {params: params})
                .then(function (resp) {
                    if (resp.data.data) {
                        app.shipping = resp.data.data;
                    }
                })
                .catch(function () {
                    console.log("error get shipping");
                });
        },
     }
}
</script>
