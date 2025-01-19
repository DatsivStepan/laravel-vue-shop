<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('categories.edit')}}
                </h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <component-form v-if="model" :model="model"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from '@component/categories/Form.vue';
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
            model: null
        }
    },
    methods: {
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/categories/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.log("error get category id:" + id);
                });
        },
     }
}
</script>