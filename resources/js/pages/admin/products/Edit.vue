<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate(translatePath + '.product_edit')}}
                </h2>
            </div>
        </div>

        <component-form v-if="model" :model="model"></component-form>
    </div>
</template>

<script>
import ComponentForm from '@component/product/Form.vue';
import '@sass/component/product-btns.scss'

export default {
    components: {
       ComponentForm,
    },
    mounted() {
        let app = this;
        let id = app.$route.params.id;
        app.getModelData(id);
    },
    data() {
        return {
            translatePath: 'products',
            model: null,
       }
    },
    methods: {
        getModelData(id) {
            let app = this;
            axios.get('/admin/custom-fields/get-fields-for-form', {params: {instanceType: 'product', instanceId: id}})
                .then(function (resp) {
                    let customFields = resp.data;
                    axios.get('/admin/products/' + id)
                        .then(function (resp) {
                            app.model = resp.data.data;
                            app.model.customFields = customFields;
                        })
                        .catch(function () {
                            console.error("error get model id:" + id);
                        });
            })
            .catch(function () {
                console.error('Get field Error');
            });
        },
     }
}
</script>