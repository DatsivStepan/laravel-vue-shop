<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate('settings.settings')}}
            </h2>
        </div>

        <settings-form v-bind:settings="settings"></settings-form>
    </div>
</template>

<script>
import SettingsForm from './Form.vue';

export default {
    components: {
       SettingsForm
    },
    mounted() {
        this.getSettings();
    },
    data: function () {
        return {
            settings: {}
       }
    },
    methods: {
        getSettings() {
            let app = this;

            axios.get('/admin/settings')
                .then(function (resp) {
                    app.settings = resp.data;
                })
                .catch(function () {
                    console.log("error get settings");
                });
        }
    }
}
</script>
