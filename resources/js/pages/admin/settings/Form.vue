<template>
    <div class="setting">
        <b-alert v-model="successAlert" variant="success" dismissible>
            {{translate('settings.configuration_saved')}}
        </b-alert>

        <form @submit.prevent="saveForm">

            <div class="panel setting-panel" v-for="(items, name) in settings" style="margin-bottom: 15px">
                <h5 class="setting-header">{{name}}</h5>

                <div class="form-group row setting-form" v-for="(item, key) in items">
                    <label for="item.id" class="col-sm-6 col-form-label setting-label">
                        <b class="setting-item">{{item.name}}</b>
                        <div class="setting-descr">{{item.key}}</div>
                    </label>

                    <div class="col-sm-6">

                        <div v-if="item.type == '0'">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" :id="item.key" :name="item.key" v-model='item.value'>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.type == '1'">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="setting-item">
                                        <input type="checkbox" :id="item.key" true-value="1"  class="setting-input" false-value="0" :name="item.key" v-model='item.value'>
                                        <label :for="item.key"></label>
                                        <span class="setting-span">Default: {{item.default_value}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.type == '2'">
                            <div class="row">
                                <div class="col-sm-6">
                                    <select v-model="item.value" class="form-control setting-dropdown">
                                        <option v-for="(param, index) in item.params.options" :value="index">
                                            {{ param }}
                                        </option>
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div v-if="item.type == '3'">
                            <div class="row">
                                <div class="col-sm-6">
                                    <select v-model="item.value" multiple class="form-control setting-dropdown">
                                        <option v-for="(param, index) in item.params.options" :value="index">
                                            {{ param }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.type == '4'">
                            <duration-input @update="item.value = $event;" :model="item.value"></duration-input>
                        </div>

                        <div v-if="item.type == '5'">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input
                                        type="number"
                                        min="0"
                                        class="form-control setting-dropdown type-number"
                                        :id="item.key"
                                        :name="item.key"
                                        v-model='item.value'
                                    >
                                </div>
                            </div>
                        </div>

                        <div v-if="item.type == '6'">
                            <div class="row">
                                <div class="col-sm-12">
                                    <image-uploader :field="item.key"
                                                    :images="Array.isArray(item.value) ? item.value : (item.value == null ? [] : [item.value])"
                                                    @changeImage="item.value = $event.files;"></image-uploader>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.type == '7'">
                            <div class="row">
                                <div class="col-sm-6">
                                    <textarea class="form-control" :id="item.key" :name="item.key" v-model='item.value'>
                                    </textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <hr />
                </div>
            </div>
            <save-button></save-button>
           <go-top :size="40" :bg-color="'#FFB800'" :boundary="300" :has-outline="false"></go-top>
        </form>

    </div>
</template>

<script>
import DurationInput from '@component/form/DurationInput.vue';
import ImageUploader from '@component/form/ImageUploader.vue';
import GoTop from '@inotom/vue-go-top';
import '@sass/component/settings.scss'
import SaveButton from '@component/form/SaveButton';


export default {
    components: {
       DurationInput,
       ImageUploader,
       GoTop,
       SaveButton
    },
    props: ['settings'],
    data: function () {
        return {
            successAlert: false
        }
    },
    methods: {
        saveForm(event) {
            event.preventDefault();

            var app = this;
            var settings = app.settings;

            axios.post('/admin/settings', settings)
                .then(function (resp) {
                    app.successAlert = true;
                    app.$auth.fetch();
                    app.$forceUpdate();
                })
                .catch(function (resp) {
                    console.log(resp);
                });
        },

    }

}
</script>
