<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('custom_fields.edit')}}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body custom-field-edit">
                <component-form :model="model" :instanceType="model.instancetype"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from '@component/custom_field/Form.vue';
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

            axios.get('/admin/custom-fields/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.log("error get custom field id:" + id);
                });
        },
     }
}
</script>