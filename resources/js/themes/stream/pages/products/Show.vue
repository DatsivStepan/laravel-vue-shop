<template>
    <div v-if="product">
        <div class="content-header clearfix">
            <h2 class="content-title">
                {{product.name}}
            </h2>
        </div>
        <div class="wrapper">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-left">
                        <h5 class="panel-title">{{ product.name }}</h5>
                        <star-rating :rating="parseFloat(product.rating)" inactive-color="#FFDFAE"
                                     active-color="#FFB800"
                                     :read-only="true" :increment="0.01" :star-size="20"
                                     :show-rating="parseFloat(product.rating) > 0" v-if="(getSettingValue('enable_reviews') == 1) && (product.enable_reviews == 1)"></star-rating>
                        <template v-if="product.description">
                            <div class="panel-descr panel-left__descr">{{ product.short_description }}</div>
                        </template>
                         <b-tabs content-class="mt-4" class="panel-tab">
                            <b-tab :title="translate('products.view.description')" active v-if="product.description">
                                <p class="tab-info" v-html="product.description"></p>
                            </b-tab>
                            <b-tab :title="translate('products.view.checkout_information')" v-if="product.is_bought && product.checkout_info"><p
                                class="tab-info" v-html="product.checkout_info"></p></b-tab>
                            <b-tab :title="translate('products.view.reviews')" @click='heightShow' v-if="(getSettingValue('enable_reviews') == 1) && (product.enable_reviews == 1)">
                                <reviews-component :click-status='activeClick' :model_id="product.id" :model_type="product.model"></reviews-component>
                            </b-tab>
                        </b-tabs>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-right">
                        <div class="panel-img panel-right__img">
                            <img :src="product.image_src" style="width:100%; height:100%;">
                        </div>
                        <h4 class="panel-price" v-if="!myProduct">{{ product.discount_formatted_price }}</h4>
                        <div
                            class="panel-oldprice panel-right__oldprice"
                            v-if="isDiscountDiffersFromRegularPrice && !myProduct"
                        >
                            {{ product.formatted_price }}
                        </div>

                        <div class="panel-product__descr panel-right__descr" v-if="product.id_number">
                            <h3 class="panel-product__title panel-right__title">
                                {{ translate('products.view.product_code') }}</h3>
                            <div class="panel-product__subtitle">{{ product.id_number }}</div>
                        </div>

                        <div
                            class="panel-product__descr"
                            v-if="custom_field.value"
                            v-for="(custom_field, key) in product.custom_fields"
                            :key="key"
                        >
                            <h3 class="panel-product__title">{{ custom_field.title }}</h3>
                            <div class="panel-product__subtitle">{{ custom_field.value }}</div>
                        </div>

                        <div class="panel-product__descr panel-right__descr" v-if="product.enable_seats && !myProduct">
                            <h3 class="panel-product__title panel-right__title">
                                {{ translate('products.view.available_seats') }}</h3>
                                <favorite-icon :product="product"></favorite-icon>
                            <div class="panel-product__subtitle"> {{ product.available_seats }} / {{
                                    product.seats
                                }}
                            </div>
                        </div>
                        <!--<div class="panel-tags panel-right__tags">
                            <a href="#" class="panel-tag">php</a>
                            <a href="#" class="panel-tag">css</a>
                            <a href="#" class="panel-tag">some tag</a>
                            / need tags
                        </div> -->
                        <div class="panel-product__descr" v-if="discountsCount">
                            <h3 class="panel-product__title">{{ translate('products.view.discounts') }}</h3>
                            <div class="panel-product__subtitle orange-item row">
                                <div
                                    v-for="(discount, key) in product.discounts"
                                    :key="key"
                                    class="col-auto"
                                >
                                    {{ discount.name }}: {{ discount.discount }}%
                                </div>
                            </div>
                        </div>
                        <to-cart-button :product="product" v-if="!myProduct"></to-cart-button>
                    </div>
                </div>
            </div>
        </div>
     </div>
</template>

<script>
import '../../sass/product-show.scss';
import ToCartButton from '@component/product/ToCartButton.vue';
import ReviewsComponent from '@modules/Reviews/Resources/js/components/reviews/Index.vue';
import StarRating from 'vue-star-rating';

export default {
    props: {
        product: {
            type: Object,
        },
        type: {
            type: String,
            default: 'show' // show / my_show
        }
    },
    data() {
        console.log('page steam show')
        console.log(this.type)
        return {
            productSetting: true,
            height: null,
            activeClick: false
        }
    },
    components: {
        ToCartButton,
        ReviewsComponent,
        StarRating
    },
    computed: {
        myProduct() {
            return this.type == 'my_show' ? true : false;
        },
        discountsCount() {
            return this.product.discounts ? this.product.discounts.length : 0;
        },
        isDiscountDiffersFromRegularPrice() {
            return !(this.product.discount_formatted_price === this.product.formatted_price);
        },
    },
    methods: {
        showHeight(val){
            this.height = val
        },
        heightShow: function(){
            this.activeClick = true
        },
    }
}
</script>
