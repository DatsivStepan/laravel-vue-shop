<template>
    <div>
        <div class="content-header">
            <h2>
                {{ translate('taxes.values.add') }}
            </h2>
        </div>

        <br/>

        <div class="card">
            <div class="card-body">
                <component-form v-bind:model="model"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from './Form.vue';
import '@sass/component/product-btns.scss'

export default {
    components: {
        ComponentForm
    },
    props: {
        field_id: {
            type: Number,
            required: true
        }
    },
    data: function () {
        return {
            model: {
                field_id: this.field_id,
                value: '',
                tax: 0
            }
        }
    }
}
</script>
