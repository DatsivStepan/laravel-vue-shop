<template>
    <div>
        <div class="content-header">

            <div class="content-align">
                <h2>
                    {{translate('discounts.edit')}}
                </h2>
            </div>

        </div>

        <div class="card">
            <div class="card-body custom-discount">
                <component-form v-bind:model="model"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from './Form.vue';
import '@sass/component/product-btns.scss'
export default {
    components: {
        ComponentForm
    },
    mounted() {
        this.getModelData();
    },
    data: function () {
        return {
            model: {
                name: '',
                time_start: null,
                time_end: null,
                used_per_user: 0,
                max_applied_products: 0,
                discount: null,
                type: 0,
                status: 1,
                min_number: null,
                max_number: null,
                description: '',
            }
        }
    },
    methods: {
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/discounts/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                    console.log(resp.data.data);
                })
                .catch(function () {
                    console.log("error getting coupon id:" + id);
                });
        },
    }
}
</script>
