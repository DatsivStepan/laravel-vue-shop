<template>
    <div>
        <div class="content-header ">
            <div class="content-align">
                <h2>
                    {{translate('vendors.edit')}}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body custom-vendor">
                <component-form :model="model"></component-form>
            </div>
        </div>

    </div>
</template>

<script>
import ComponentForm from '@component/vendors/Form.vue';
import '@sass/component/product-btns.scss';

export default {
    components: {
       ComponentForm,
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

            axios.get('/admin/vendors/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.error("error get vendor id:" + id);
                });
        },
     }
}
</script>