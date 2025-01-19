import Vue from 'vue';

export default {
    namespaced: true,

    state() {
        return {

        };
    },

    actions: {
        fetch(data) {
            return Vue.auth.fetch(data);
        },

        refresh(data) {
            return Vue.auth.refresh(data);
        },

        login(ctx, data) {
            data = data || {};

            return new Promise((resolve, reject) => {
                Vue.auth.login({
                    url: 'auth/login',
                    data: data.body,
                    remember: data.remember,
                    staySignedIn: data.staySignedIn,
                })
                    .then((res) => {
                        if (data.remember) {
                            Vue.auth.remember(JSON.stringify({
                                name: ctx.getters.user.first_name
                            }));
                        }

                        Vue.router.push({
                            name: ctx.getters.user.type + '-landing'
                        });

                        resolve(res);
                    }, reject);
            });
        },

        logout(ctx) {
            return Vue.auth.logout();
        },
    },

    getters: {
        countProductInCart() {
            return Vue.auth.user().countProductInFavorite;
        },
        userSettings() {
            return Vue.auth.user().countProductInFavorite;
        },
        user() {
            return Vue.auth.user();
        },

        impersonating() {
            return Vue.auth.impersonating();
        }
    }
}
