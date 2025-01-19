<template>
    <div>
        <div class="content-header">
            <h2>
                {{translate(translatePath + '.create_product')}}
            </h2>

        </div>

        <component-form :model="model"></component-form>

    </div>
</template>

<script>
import ComponentForm from '@component/product/Form.vue';

export default {
    components: {
       ComponentForm
    },
    data() {
       return {
            translatePath: 'products',
            model: {
                name: '',
                visible: 1,
                description: '',
                seats: 0,
                enable_sessions: false,
                enable_shipping: false,
                enable_tax: false,
                show_items: false,
                image_src: null,
                video_src: null,
                featured_image_src: null,
                max_seats_per_user: 0,
                subscription_cancellation_action: 0,
                subscription_expiration_action: 0,
                categories: {},
                instructors: {},
                customFields: {},
                theme_key: 'default',
                enable_reviews: false,
                enable_reviews_approval: false,
                enrol_start: 0,
                enrol_on: 0,
                enrol_end: 0,
                billing_type: 'regular',
            }
       }
    },
    mounted() {
        let app = this;
        app.getCustomFields();
    },
    methods: {
        getCustomFields() {
            let app = this;
            axios.get('/admin/custom-fields/get-fields-for-form', {params: {instanceType: 'product', instanceId: 0}})
                .then(function (resp) {
                    app.model.customFields = resp.data;
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        }
    }
}
</script>
