<template>
    <div>
        <div class="content-header ">
                <div class="content-align">
                <h2>
                    {{ translate('payments.edit') }}
                </h2>
            </div>
        </div>
        <h5
            class="payment-subtitle"
            v-if="model && model.webhook"
            @click="copy(model.webhook)"
        >
            {{ translate('payments.webhook.url') }}: <span>{{ model.webhook }}</span>
            <b-icon icon="files"></b-icon>
        </h5>

        <component-form v-if="model" :model="model"></component-form>

    </div>
</template>

<script>
import ComponentForm from '../../components/Form.vue';
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
            model: null,
        }
    },
    methods: {
        copy(text) {
            let app = this;
            this.$copyText(text).then(function (e) {
                alert(app.translate('system.copied'))
            }, function (e) {
                alert(app.translate('system.not_copy'))
            })
        },
        getModelData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/admin/payments/' + id)
                .then(function (resp) {
                    app.model = resp.data.data;
                })
                .catch(function () {
                    console.log("error get payment id:" + id);
                });
        }
    }
}
</script>
