<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{ translate(translatePath + '.create') }}
                </h2>
            </div>
        </div>

        <component-form :model="model"></component-form>

    </div>
</template>

<script>
import ComponentForm from '../../components/Form.vue';
import '@sass/component/product-btns.scss';

export default {
    components: {
        ComponentForm
    },
    data: function () {
        return {
            translatePath: 'payments',
            model: {
                type: '',
                name: '',
                currency: '',
                status: 0,
                settings: {}
            }
        }
    }
}
</script>
