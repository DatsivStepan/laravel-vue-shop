<template>
    <div class="to-cart-buttons-block full-width" v-if="!product.enable_sessions">
            <div v-if="enableWaitlist" class="custom-row-btn d-flex justify-content-between">
                <b-button class="custom-col-btn ml-auto"  :disabled="disabledWaitlistButton"
                        @click="addToWaitlist(1)">
                    <span v-if="disabledWaitlistButton">
                        {{ translate('products.added_to_waitlist') }}
                    </span>
                    <span v-else>
                        {{ translate('products.add_to_waitlist') }}
                    </span>
                </b-button>
            </div>

            <div class="custom-row-btn d-flex justify-content-between"
                 v-else-if="product.billing_type === 'subscription' && getSettingValue('enable_subscription') == 1">
                <b-button
                    block size="sm"
                    @click="$router.push({ name : 'subscribe-product', params: { product_id: product.id }})"
                    variant="primary"
                    tag="button"
                    class="custom-col-btn ml-auto"
                >
                    <span>
                        {{ translate('subscriptions.subscribe_now') }}
                    </span>
                </b-button>
            </div>

            <div class="custom-row-btn d-flex justify-content-between" v-else-if="!enableBuyOnlyOneProduct && !enableSeats && product.in_cart">
                <b-button class="panel-card-btn custom-col-btn ml-auto" block size="sm" variant="primary" disabled>
                    {{ translate('products.addedtocart') }}
                </b-button>
            </div>

            <div v-else class="custom-row-btn d-flex justify-content-between">
                <div class="custom-col-btn custom-col-seats" v-if="enableSeats">
                    <b-input-group class="inp-group-seats">
                        <div class="button-option w-70 justify-content-center">
                            <span class="minus" :class="{disable:disableButton}" @click="setValNumberMinus"><ion-icon name="chevron-back"></ion-icon></span>
                            <b-form-input type="number" min="0" v-model="vendorSeats"></b-form-input>
                            <span class="plus" @click="setValNumberPlus"><ion-icon name="chevron-forward"></ion-icon></span>
                        </div>
                        <b-input-group-append class="w-30">
                            <b-button variant="outline-primary" @click="seatsToCard()">
                                <ion-icon name="basket"></ion-icon>
                                <span class="checkmark"  v-if="disabledCartButton">
                                    <ion-icon name="checkmark-circle"></ion-icon>
                                </span>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </div>
                <div v-if="enableBuyOnlyOneProduct" class="custom-col-btn ml-auto">
                    <b-button class="panel-card-btn" block size="sm" variant="primary" @click="addToCard(1, true)">
                        <span>
                            {{ translate('products.by_now') }}
                        </span>
                    </b-button>
                </div>
                <div v-if="!enableBuyOnlyOneProduct" class="custom-col-btn ml-auto" >
                    <b-button class="panel-card-btn" block size="sm" variant="primary" @click="addToCard(1)" :disabled="product.in_cart" product.in_cart>
                        <span>
                            {{ product.in_cart ? translate('products.addedtocart') : translate('products.addtocart') }}
                        </span>
                    </b-button>
                </div>

            </div>

    </div>
</template>

<script>
import '@sass/component/product-btns.scss'

export default {
    props: {
        product: {
            type: Object,
            required: true
        },
        showSead: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            vendorSeats: 0,
            disableButton: true
        }
    },
    computed: {
        enableBuyOnlyOneProduct: {
            get: function () {
                return this.getSettingValue('buy_only_one_product') == 1;
            }
        },
        enableWaitlist: {
            get: function () {
                let app = this;
                return app.getSettingValue('enable_waitlist') && (app.product.enable_seats && !app.product.available_seats) ? true : false
            }
        },
        disabledWaitlistButton: function () {
            return this.product.in_waitlist;
        },
        disabledCartButton: function () {
            let app = this;
            return app.product.in_cart || (app.product.enable_seats && !app.product.available_seats) ? true : false;
        },
        enableSeats: function () {
            let app = this;
            return app.product.enable_seats && app.getSettingValue('enable_seats_vendors') && app.showSead;
        }
    },
    methods: {
        setValNumberPlus: function() {
            this.vendorSeats = parseInt(this.vendorSeats) + 1;
            this.disableButton = false;
        },
        setValNumberMinus: function() {
            this.vendorSeats = parseInt(this.vendorSeats) - 1;
            if(this.vendorSeats === 0){
                this.disableButton = true;
            }
        },
        seatsToCard() {
            let app = this;
            if (app.vendorSeats) {
                app.addToCard(app.vendorSeats);
            }
        },
        addToCard(seats) {
            let app = this;
            let id = app.product.id;
            let params = {
                id: id,
                seats: seats,
                delete_other: app.enableBuyOnlyOneProduct ? 1 : 0
            };

            axios.post('/orders/add-to-cart', params)
                .then(function (resp) {
                    if (resp.data.status) {
                        app.$store.state.cartTabActive = '';

                        if (app.enableBuyOnlyOneProduct) {
                            app.$store.commit('removeProductFromCart', app.$store.state.productInCart)
                            app.$store.commit('setProductToCart', 1);
                            app.$router.push({ name : 'checkout' });
                        } else {
                            app.product.in_cart = 1;
                            app.$store.commit('setProductToCart', 1);
                            app.$alert(resp.data.message, '', '');
                        }
                    } else {
                        app.$alert(resp.data.message, '', '');
                    }
                })
                .catch(function () {
                    console.log("error add to cart id:" + id);
                });
        },
        addToWaitlist() {
            let app = this;
            let id = app.product.id;
            app.disabledWaitlistButton = true;
            axios.post('/products/add-to-waitlist/' + id)
                .then(function (resp) {
                    if (resp.data.status) {
                        app.product.in_waitlist = 1;
                        app.disabledWaitlistButton = true;
                        app.$alert(resp.data.message, '');
                    } else {
                        app.disabledWaitlistButton = false;
                        app.$alert(resp.data.message, '');
                    }
                })
                .catch(function () {
                    console.log("error add to cart id:" + id);
                });
        },
    }
}
</script>
