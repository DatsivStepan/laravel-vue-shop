<template>
    <div class="col-12 col-md-6 col-lg-4 list-view-mode">
        <div class="product-info">
            <div class="col-auto p-0 d-flex justify-content-between align-items-center list-title">
                <div class="d-flex flex-column justify-content-between align-items-start list-mode-width">
                    <h4 class="title mb-28">{{ product.name }}</h4>
                    <h3 class="product-info-price" v-if="type == 0">
                        {{ product.formatted_price }}
                    </h3>
                </div>
                <div class="p-1 d-flex flex-column align-self-stretch justify-content-between align-items-end hidden-list-view-mode">
                    <favorite-icon :product="product" style='padding-right: 5px' class="align-self-end"></favorite-icon>
                    <div class="product-info-tags" v-if="type == 0">
                        <span class="new">New</span>
                    </div>
                </div>
            </div>
            <div class="col-auto p-0 list-mode-flex justify-content-start">
                <div class="line"></div>
                <div class="img" :style="{'background-image': 'url(' + product.image_src + ')'}"></div>
                <div class="d-flex justify-content-between align-items-center mb-16 product-info-item">
                    <star-rating :rating="parseFloat(product.rating)" :show-rating="parseFloat(product.rating) > 0" inactive-color="#FFDFAE"
                        active-color="#FFB800" :read-only="true" :increment="0.01" :star-size="20" v-if="(getSettingValue('enable_reviews') == 1) && (product.enable_reviews == 1)"></star-rating>
                    <router-link v-if="type == 0" :to="{ name : 'product-show', params: { id: product.id }}"
                        class="product-info-details ml-auto">
                        {{ translate('products.details') }}
                        <ion-icon name="chevron-forward"></ion-icon>
                    </router-link>
                </div>
            </div>
            <div class="col-auto list-view-favorite-button w-5">
                <favorite-icon :product="product"></favorite-icon>
            </div>
            <div class="col-auto p-0 list-mode-flex flex-none buttons-width">

                <div class="row w-100 m-0">
                    <div class="col p-0" v-if="type == 0" >
                        <to-cart-button :product="product"></to-cart-button>
                    </div>

                    <div class="detalis-col ml-auto" v-if="type == 1">
                        <router-link  :to="{ name : 'product-show-my', params: { id: product.id,  type: 'my_show' }}"
                            class="btn product-button-active btn-size">
                            {{ translate('products.details') }}
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FavoriteIcon from '@component/product/ToFavoriteIcon.vue';
import ToCartButton from '@component/product/ToCartButton.vue';
import StarRating from 'vue-star-rating';
import '@sass/component/product-btns.scss'

export default {
    props: {
        product: {
            type: Object,
            required: true
        },
        type: {
            type: Number,
            default: 0 // 0 - to cart button; 1 - details
        },
    },
    components: {
        ToCartButton,
        StarRating,
        FavoriteIcon
    },
}
</script>
