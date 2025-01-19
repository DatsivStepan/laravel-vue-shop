<template>
    <div>
        <div class="content-header">
            <div class="content-align">
                <h2>
                    {{translate(translatePath + '.create')}}
                </h2>
            </div>

        </div>

        <br />

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
    data: function () {
        return {
            translatePath: 'coupons',
            model: {
                code: '',
                timestart: null,
                timeend: null,
                usedperuser: '',
                usedcount: '',
                discount: '',
                type: 0,
                status: 1,
            }
        }
    }
}
</script>
