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
import ComponentForm from '@component/categories/Form.vue';
import '@sass/component/product-btns.scss';
export default {
    components: {
        ComponentForm
    },
    data: function () {
        let app = this;
        return {
            translatePath: 'categories',
            model: {
                name: '',
                description: '',
                id_number: '',
                parent_id: app.$route.params.parentId ? app.$route.params.parentId : 0,
                visible: 1,
                image_src: null,
            }
        }
    }
}
</script>