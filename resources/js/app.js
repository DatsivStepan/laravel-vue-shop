require('./bootstrap');

import 'es6-promise/auto'
import axios from 'axios'
import Vue from 'vue'

import VueRouter from 'vue-router';
import VueAuth from '@websanova/vue-auth'
import VueAxios from 'vue-axios'
import LoadScript from 'vue-plugin-load-script';
import VueSimpleAlert from "vue-simple-alert";
import VueScrollTo from "vue-scrollto";
import Vue2Crumbs from 'vue-2-crumbs'
import auth from './auth'
import routers from './routers';

// Temporarily
import VueMoment from 'vue-moment';
// Temporarily
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue';
import Table from './components/grid/Table.vue';
import Loading from './components/Loader.vue';
import store from './store/store';
import mixinData from './mixin';
import VueClipboard from 'vue-clipboard2'
// Install BootstrapVue
import Layout from './pages/Layout';
import vSelect from 'vue-select'



window.Vue = Vue;

Vue.prototype.$http = axios;
Vue.use(VueRouter);
Vue.use(LoadScript);
Vue.use(VueSimpleAlert);
Vue.use(VueMoment);
Vue.use(VueScrollTo);

Vue.use(Vue2Crumbs);
// Set Vue router
Vue.router = routers;

window.tranlate = require('./VueTranslation/Translation').default.translate;
Vue.prototype.translate = require('./VueTranslation/Translation').default.translate;

// Set Vue authentication
Vue.use(VueAxios, axios);
axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api`;
Vue.use(VueAuth, auth);

Vue.use(IconsPlugin);
Vue.use(BootstrapVue);

VueClipboard.config.autoSetContainer = true;
Vue.use(VueClipboard)

/*Vue.component('vue-resource', require('vue-resource')); */
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('table-grid', Table);
Vue.component('loading', Loading);
Vue.component('app-basket', require('./components/app-basket/appBasket.vue').default);
Vue.component('app-favorite', require('./components/app-favorite-items/appFavoriteItems.vue').default);
Vue.component('v-select', vSelect)
Vue.mixin(mixinData);

Vue.component('index', Layout);

const app = new Vue({
    data: { loading: false },
    el: '#app',
    router: routers,
    store: store
});

routers.beforeEach((to, from, next) => {
    app.loading = true
    next();
})

routers.afterEach((to, from, next) => {
    setTimeout(() => app.loading = false, 150) // timeout for demo purposes
})
