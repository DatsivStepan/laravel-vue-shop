import Vue from 'vue'
import Vuex from 'vuex'
import auth from "./auth";

Vue.use(Vuex)

const store = new Vuex.Store({
    state: {
        user: [],
        roles: [],
        permissions: [],
        settings: [],
        productInCart: 0,
        productInFavorite: 0,
        cartTabActive: '',
        userSettings: {},
    },
    modules: {
        auth
    },
    mutations: {
        setPermission (state, permissions) {
            state.permissions = permissions;
        },
        setRoles (state, roles) {
            state.roles = roles;
        },
        setUser (state, user) {
            state.user = user;
        },
        setSettings (state, settings) {
            state.settings = settings;
        },
        setUserSettings (state, settings) {
            state.userSettings = settings;
        },
        setProductToCart (state, productCount) {
            state.productInCart += productCount;
        },
        removeProductFromCart (state, productCount) {
            state.productInCart -= productCount;
        },
        setProductToFavorite (state, productCount) {
            state.productInFavorite += productCount;
        },
        removeProductFromFavorite (state, productCount) {
            state.productInFavorite -= productCount;
        },
        setActiveCartTab (state, tabKey) {
            state.cartTabActive = tabKey;
        },
    }
});

export default store;
