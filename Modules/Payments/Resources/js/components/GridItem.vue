<template>
    <div class="row">
        <div v-for="(item, index) in items" :key="index" class="col-sm-6 col-lg-4 px-2">
            <div class="card mb-3 payment-card">
                <div class="card-header payment-header bg-transparent" style="border:0px;">
                    <div>
                        <h5 class="payment-title">{{ item.name }}</h5>
                        <p class="payment-subtitle">{{ item.type }}</p>
                    </div>
                    <actions-dropdown :dimension="'horizontal'">
                        <li v-for="action in item.actions">
                            <router-link v-if="action.type == 0" :title="action.title"
                                         :to="{name: action.params.route, params: action.params.params}">
                                {{ action.title }}
                            </router-link>
                            <a v-else-if="action.type == 1" @click="callMethod(action.params)">
                                {{action.title}}
                            </a>
                        </li>
                    </actions-dropdown>
                </div>
                 <div class="payment-icon">
                    <img :src="item.icon">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ActionsDropdown from './ActionsDropdown.vue';
import '@sass/component/admin-area.scss'


export default {
    components: {
      ActionsDropdown
    },
    props: {
        items: {
            type: [Array, Object],
        },
    },
    data: function () {
      return {}
    },
    methods: {
        getModels(page) {
            let app = this
            app.$emit('get-models')
        },
        deleteEntry(params) {
            let app = this;
            let url = params['url'];
            let id = params['id'];

            let confirmOption = {
                cancelButtonText: app.translate('system.confirm.no'),
                confirmButtonText: app.translate('system.confirm.yes')
            }
            this.$confirm(app.translate('system.confirm.question.you_want_delete'), '', 'warning', confirmOption).then(() => {
                axios.delete(url + id).then(function (resp) {
                    app.getModels();
                })
                    .catch(function (resp) {
                        console.error('error delete');
                    });
            });
        },
        callMethod(action) {
            this[action.methodName](action.params);
        },
        actionApi(params) {
            let app = this;
            axios[params.method](params.route, params.routeParams).then(function (resp) {
                app.getModels();
            })
                .catch(function (resp) {
                    console.error('error call route ' + params.route);
                });
        },

    }
}
</script>
