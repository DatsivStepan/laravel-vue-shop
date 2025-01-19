<template>
    <div class="container">
<!--        <div class="card card-default">
            <div class="card-header">{{translate('auth.login')}}</div>
            <div class="card-body">
                <div class="alert alert-danger" v-if="has_error">
                    <p>{{translate('auth.errorcredential')}}</p>
                </div>
                <form autocomplete="off" @submit.prevent="loginForm" method="post">
                    <div class="form-group">
                        <label for="email">{{translate('auth.email')}}</label>
                        <input type="email" id="email" class="form-control" placeholder="user@example.com" v-model="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">{{translate('auth.password')}}</label>
                        <input type="password" id="password" class="form-control" v-model="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{translate('auth.login')}}</button>
                </form>
            </div>
        </div>-->
    </div>
</template>
<script>
    export default {
        mounted() {

            var app = this;
            let auth_token = app.$route.query.auth_token;
            let connection_id = app.$route.query.connection_id;
            if (auth_token && connection_id) {
                var params = {
                    auth_token: auth_token,
                    connection_id: connection_id
                };
                app.login(params);
            }
        },
        methods: {
            login(params) {
                let app = this;

                let tokenDefault = 'auth_token_default_' + params.connection_id;
                let staySignedInKey = 'auth_stay_signed_in_' + params.connection_id;

                app.$auth.options.tokenDefaultKey = tokenDefault;
                app.$auth.options.staySignedInKey = staySignedInKey;

                localStorage.removeItem(tokenDefault);
                localStorage.removeItem(staySignedInKey);

                app.$auth.login({
                    params: params,
                    rememberMe: true,
                    fetchUser: true
                }).then(() => {
                    this.reloadUserData(app.$auth.user());
                })
            }
        }
    }
</script>
