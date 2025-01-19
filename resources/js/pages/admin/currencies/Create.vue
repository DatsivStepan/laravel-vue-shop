<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate('currency.add')}}
                </h2>
            </div>

        </div>

        <br />

        <div class="card">
            <div class="card-body currencies-custom">
                <component-form :model="model" mode="add"></component-form>
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
    data: function () {
        return {
            model: {
                code: null,
                exchange_rate: 1,
                format: '',
                is_default: false,
            }
        }
    }
}
</script>
