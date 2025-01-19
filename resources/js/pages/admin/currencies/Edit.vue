<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('currency.edit')}}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body currencies-custom">
                <component-form v-if="model" :model="model" mode="edit"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from './Form.vue';
import '@sass/component/product-btns.scss';

export default {
    components: {
        ComponentForm
    },
    mounted() {
        this.getModelData();
    },
    data: function () {
        return {
            model: null
        }
    },
    methods: {
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/currencies/' + id)
                .then(function (resp) {
                    app.model = resp.data;
                })
                .catch(function () {
                    console.error("error getting coupon id:" + id);
                });
        },
    }
}
</script>
