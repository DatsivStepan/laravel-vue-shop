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
            <div class="card-body custom-field-create">
                <component-form :model="model" :instanceType="instanceType"></component-form>
            </div>
        </div>
    </div>
</template>

<script>
import ComponentForm from '@component/custom_field/Form.vue';
import '@sass/component/product-btns.scss';

export default {
    components: {
       ComponentForm
    },
    data: function () {
       return {
            instanceType: '',
            translatePath: 'custom_fields',
            model: {
                title: '',
                fieldtype: 0,
                required: 0,
                options: '',
                instance_type: '',
            }
       }
    },
    mounted() {
        let app = this;
        app.instanceType = app.$route.params.instanceType;
        app.model.instance_type = app.$route.params.instanceType;
    },
}
</script>