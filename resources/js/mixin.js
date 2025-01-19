import store from './store/store';

const data = {
    methods: {
        checkRole: function (roles) {
            var user = this.$auth.user();
            if (roles && roles instanceof Array && roles.length > 0) {
                if ((typeof user !== "undefined") && (typeof user.roles !== "undefined")) {
                    var userRoles = user.roles;
                    if (userRoles) {
                        for (const userRole in userRoles) {
                            if (roles.includes(userRoles[userRole])) {
                                return true;
                            }
                        }
                    } else {
                        console.error(`User not have roles!`);
                    }
                } else {
                    console.error(`User not found!`);
                }
            } else {
                console.error(`Need roles! Like ['role_1', 'role_2']"`);
            }

            return false;
        },

        checkPermission: function (permissions) {
            var user = this.$auth.user();
            if (permissions && permissions instanceof Array && permissions.length > 0) {
                if ((typeof user !== "undefined") && (typeof user.permissions !== "undefined")) {
                    var userPermissions = user.permissions;
                    if (userPermissions) {
                        for (const userRole in userPermissions) {
                            if (permissions.includes(userPermissions[userRole])) {
                                return true;
                            }
                        }
                    } else {
                        console.error(`User not have permissions!`);
                    }
                } else {
                    console.error(`User not found!`);
                }
            } else {
                console.error(`Need permissions! Like ['permission_1', 'permission_2']"`);
            }

            return false;
        },

        getSettingValue: function (key) {
            let user = this.$auth.user();

            if (key) {
                if ((typeof user !== "undefined") && (typeof user.settings !== "undefined")) {
                    if (typeof user.settings[key] !== "undefined") {
                        return user.settings[key];
                    } else {
                        console.error(`Setting key not found!`);
                    }
                } else {
                    console.error(`User OR Settings not found!`);
                }
            } else {
                console.error(`Need key`);
            }

            return false;
        },

        getUserSettingValue: function (key) {
            let user = this.$auth.user();

            if (key) {
                if ((typeof user !== "undefined") && (typeof user.userSettings !== "undefined")) {
                    if (typeof user.userSettings[key] !== "undefined") {
                        return user.userSettings[key];
                    } else {
                        console.error(`User Setting key not found!`);
                    }
                } else {
                    console.error(`User OR User Settings not found!`);
                }
            } else {
                console.error(`Need key`);
            }

            return false;
        },

        updateUserSettings: function ($settings) {
            let user = this.$auth.user();
            user.userSettings = $settings;
            this.$auth.user(user);
        },

        reloadUserData: function (user) {
            if (user) {
                store.commit('setUser', user);
                store.commit('setPermission', user.permissions);
                store.commit('setRoles', user.roles);
                store.commit('setSettings', user.settings);
                store.commit('setProductToCart', user.countProductInCart);
                store.commit('setProductToFavorite', user.favorites_count);
                store.commit('setUserSettings', user.userSettings);
            }
        },

        printInvoice(params) {
            let app = this;
            axios.get('/admin/sales/print-invoice/' + params.id)
                .then(function (resp) {
                    if (resp.data.status) {
                        app.downloadFile(resp.data.path, resp.data.name)
                    }
                })
                .catch(function () {
                    console.error("print error");
                });
        },

        downloadFile(fileUrl, fileName = 'file.pdf') {
            console.log(fileUrl)
            axios({
                url: fileUrl,
                method: 'GET',
                responseType: 'blob',
            }).then((response) => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', fileName);
                document.body.appendChild(fileLink);
                fileLink.click();
            });
        },
    },
};

export default data;
