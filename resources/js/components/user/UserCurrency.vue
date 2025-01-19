<template>
    <div class="user-currency" v-if="isMoreOne">
        <label for="code">{{ translate('currency.select_currency') }}</label>
        <div class="for-icon">
            <v-select
                id="code"
                v-model="selectedCurrency"
                class="style-chooser"
                placeholder="Select currencies"
                :options="currencies"
                @input="select"
                label="name">
                    <template v-slot:option="option">
                        {{ option.name }}
                    </template>
            ></v-select>
        </div>
    </div>

</template>

<script>
import * as qs from "qs";
import api from "../../api";
import store from "../../store/store";


export default {
    name: "UserCurrency",

    data() {
        return {
            currencies: [],
            selectedCurrency: null,
            selectStatus: false,
        }
    },

    mounted() {
        this.selectedCurrency = store.state.userSettings.currency
        this.getAvailableCurrencies()
    },
    computed: {
        isMoreOne() {
            return this.currencies.length > 1;
        }
    },
    methods: {
        getAvailableCurrencies() {
            let app = this;
            let params = {
                filter: {active: true}
            }

            axios.get(api.urls.admin.currencies.getActiveCurrencies, {
                params: params,
                paramsSerializer: params => {
                    return qs.stringify(params)
                }
            })
                .then(function (resp) {
                    app.currencies = resp.data
                })
                .catch(function () {
                    console.error('Get Options Error');
                });
        },

        select(value) {
            axios.patch(api.urls.admin.users.settings.setCurrency, {code: value.code})
                .then((res) => {
                    this.updateUserSettings(res.data)
                    store.commit('setUserSettings', res.data);
                    this.$emit('changeCurrency');
                })
        },
    }
}
</script>
