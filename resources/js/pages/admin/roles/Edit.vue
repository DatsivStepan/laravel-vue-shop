<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('roles.edit')}}
                </h2>
            </div>
        </div>

        <div class="card rounded-0">
            <div class="card-body">
                <component-form :model="model"></component-form>
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
            model: {
                id: null,
                name: '',
            },
       }
    },
    methods: {
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/roles/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.log("error get model id:" + id);
                });
        },
     }
}
</script>