<template>
<!--  -->
    <hooper :autoPlay="true" @slide='showAlert' ref='hooper' :playSpeed="3000" :itemsToShow="1">
        <slide v-for="product in products" :key="product.id">
            <div class="bg-images" v-bind:style="{ backgroundImage: 'url(' + product.image_src + ')' }" style="width: 100%">
                    <star-rating :rating="parseFloat(product.rating)" :read-only="true" :increment="0.01"
                        :star-size="17" :show-rating="parseFloat(product.rating) > 0"
                        v-if="(getSettingValue('enable_reviews') == 1) && (product.enable_reviews == 1)"></star-rating>
                <div>
                    <h1>{{ product.name }}</h1>
                    <h3 class="product-info-price">
                        {{ product.formatted_price }}
                    </h3>
                </div>
                <div class="position-absolute button-product-slider ">
                    <div class="row m-0">
                        <div class="col-6 p-0">
                            <to-cart-button :product="product" :showSead="false"></to-cart-button>
                        </div>
                    </div>
                </div>
            </div>
        </slide>
        <hooper-navigation slot="hooper-addons"></hooper-navigation>
        <hooper-pagination slot="hooper-addons"></hooper-pagination>
    </hooper>
</template>

<script>
import 'hooper/dist/hooper.css';
import {Hooper, Slide, Navigation as HooperNavigation, Pagination as HooperPagination} from 'hooper';
import ToCartButton from '@component/product/ToCartButton.vue';
import StarRating from 'vue-star-rating';

export default {
    props: {
        products: {
            type: Array,
            required: true
        }
    },
    components: {
        Hooper,
        Slide,
        HooperNavigation,
        HooperPagination,
        ToCartButton,
        StarRating
    },
    methods: {
        showAlert(){
            this.$refs.hooper.updateWidth()
        },

    }
}
</script>
