<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('notifications.edit')}}
                </h2>
            </div>

        </div>

        <div class="card">
            <div class="card-body">

                <component-form v-bind:model="model"></component-form>

            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from './Form.vue';
import api from "../../../api";
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
                subject: '',
                body: '',
                status: true,
                tags: [],
            }
        }
    },
    methods: {
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get(api.urls.admin.notifications.show + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.log("error getting coupon id:" + id);
                });
        },
    }
}
</script>
