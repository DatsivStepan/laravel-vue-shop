<template>
    <button class="favorite-button" @click="favorite()" v-if="getSettingValue('enabled_favorites_products') == 1">
        <ion-icon name="heart" class="md hydrated" :class="{'icon-active': product.in_favorite == 1}" ></ion-icon>
    </button>
</template>
<style>
    .favorite-button .icon-active {
        color: #FFB800 !important;
    }
</style>
<script>
export default {
    props: {
        product: {
            type: Object,
            required: true
        }
    },
    data() {
        return {}
    },
    computed: {},
    methods: {
        favorite() {
            let app = this;
            if (app.product.in_favorite == 1) {
                app.removeFromFavorite();
                console.log('remove');
            } else {
                app.addToFavorite();
                console.log('dda');
            }
        },
        addToFavorite() {
            let app = this;
            let product_id = app.product.id;
            let user_id = app.$auth.user().id;
            axios.post('/products-favorites', {product_id: product_id, user_id: user_id})
                .then(function (resp) {
                    app.product.in_favorite = 1;
                    app.$store.commit('setProductToFavorite', 1);
                })
                .catch(function () {
                    console.log("error add to favorite product id:" + product_id);
                });
        },
        removeFromFavorite() {
            let app = this;
            let product_id = app.product.id;
            let user_id = app.$auth.user().id;
            axios.delete('/products-favorites', {params: {product_id: product_id, user_id: user_id}})
                .then(function (resp) {
                    app.product.in_favorite = 0;
                    app.$store.commit('removeProductFromFavorite', 1);
                })
                .catch(function () {
                    console.log("error add to favorite product id:" + product_id);
                });
        },
    }
}
</script>
