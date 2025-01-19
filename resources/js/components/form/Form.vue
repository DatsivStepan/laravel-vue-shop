<template>
    <div class="">
        <form class="form" @submit="saveForm()">
            <slot></slot>
            <save-button></save-button>
        </form>
    </div>
</template>

<script>
//    config {
//      redirectRoute: 'redirect route name, after save',
//      apiBaseUrl: 'url for create|update url api',
//    }
import '../../../sass/component/product-btns.scss'
import SaveButton from './SaveButton';

export default {
    components: {
        SaveButton
    },
    props: ['model', 'config'],
    methods: {
        saveForm() {
            event.preventDefault();

            let app = this;
            app.$emit('before-save');

            let newModel = app.model;
            if (typeof app.model.id !== "undefined") {
                app.update(newModel);
            } else {
                app.create(newModel);
            }
        },
        create(newModel) {
            let app = this;
            axios.post(app.config.apiBaseUrl, newModel)
                .then(function (resp) {
                    app.$router.push(app.config.redirectRoute);
            })
            .catch(error => {
                if (error.response.status == 422){
                    app.$emit('add-validation-errors', error.response.data.errors);
                }
            })
        },
        update(newModel) {
            let app = this;
            axios.patch(app.config.apiBaseUrl + '/' + app.model.id, newModel)
                .then(function (resp) {
                    app.$router.push(app.config.redirectRoute);
                })
                .catch(error => {
                    if (error.response.status == 422){
                        app.$emit('add-validation-errors', error.response.data.errors);
                    }
                })
        }
    }
}
</script>