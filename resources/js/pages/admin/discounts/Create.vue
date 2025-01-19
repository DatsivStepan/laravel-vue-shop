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
            <div class="card-body custom-discount">
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
            translatePath: 'discounts',
            model: {
                name: '',
                time_start: null,
                time_end: null,
                used_per_user: 0,
                max_applied_products: 0,
                discount: null,
                type: 0,
                status: 1,
                min_number: null,
                max_number: null,
                description: '',
            }
        }
    }
}
</script>
