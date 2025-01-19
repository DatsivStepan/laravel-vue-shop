<template>
    <div>
        <div class="content-header">
                <div class="content-align">
                    <h2>
                        {{translate('coupons.edit')}}
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
                code: '',
                timestart: null,
                timeend: null,
                usedperuser: '',
                usedcount: '',
                discount: '',
                type: 0,
                status: 1,
            },
        }
    },
    methods: {
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/coupons/' + id)
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
