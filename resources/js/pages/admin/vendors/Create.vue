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
            <div class="card-body custom-vendor">
                <component-form v-bind:model="model"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from '@component/vendors/Form.vue';
import '@sass/component/product-btns.scss';


export default {
    components: {
       ComponentForm
    },
    data: function () {
       return {
            translatePath: 'vendors',
            model: {
                name: '',
                idnumber: '',
                type: '',
                email: '',
                company: '',
                url: '',
                address: '',
                status: 0,
                image_src: null,
            }
       }
    }
}
</script>