<template>
    <div v-if="product">
        <div class="content-header clearfix">
            <h2 class="content-title">
                {{ product.name }}
            </h2>
        </div>

        <div class="panel">
            <div class="row">
                <div class="col-sm-6 panel-custom-col">
                    <div class="panel-img">
                        <img :src="product.image_src">
                    </div>
                </div>
                <div class="col-sm-6 panel-custom-col-secondary">
                    <h5 class="panel-title">{{ product.name }}</h5>
                    <div class="panel-prices">
                        <div class="row" v-if="!myProduct">
                            <div class="col-sm-auto">
                                <h4 class="panel-price">{{ product.discount_formatted_price }}</h4>
                                <div
                                    class="panel-oldprice panel-right__oldprice"
                                    v-if="isDiscountDiffersFromRegularPrice"
                                >
                                    {{ product.formatted_price }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <star-rating :rating="parseFloat(product.rating)" :read-only="true" :increment="0.05"
                                 inactive-color="#FFDFAE" active-color="#FFB800" :star-size="20" v-if="(getSettingValue('enable_reviews') == 1) && (product.enable_reviews == 1)"></star-rating>

                    <div class="panel-descr">
                        {{ product.short_description }}
                    </div>
                    <!--<div class="panel-tags">
                        <a href="#" class="panel-tag">php</a>
                        <a href="#" class="panel-tag">css</a>
                        <a href="#" class="panel-tag">some tag</a>
                    </div> -->
                    <div class="row" v-if="!myProduct">
                        <div class="col-12">
                            <to-cart-button class="panel-btn" :product="product"></to-cart-button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 p-0">
                    <div class="panel-product__about">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel-product__wrap">
                                    <div class="col-lg-6 col-sm-12 panel-custom-col" v-if="product.id_number">
                                        <div class="panel-product__descr">
                                            <h3 class="panel-product__title">
                                                {{ translate('products.view.product_code') }}</h3>
                                            <div class="panel-product__subtitle">{{ product.id_number }}</div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 panel-custom-col" v-if="product.enable_seats && !myProduct">
                                        <div class="panel-product__descr">
                                            <h3 class="panel-product__title">
                                                {{ translate('products.view.available_seats') }}</h3>
                                            <div class="panel-product__subtitle">
                                                {{ product.available_seats }} / {{ product.seats }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 panel-custom-col" v-if="discountsCount">
                                        <div class="panel-product__descr" >
                                            <h3 class="panel-product__title">
                                                {{ translate('products.view.discounts') }}</h3>
                                            <div class="panel-product__subtitle orange-item row">
                                                <div v-for="(discount, key) in product.discounts" :key="key"
                                                     class="col-auto">{{ discount.name }}: {{ discount.discount }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 panel-custom-col"
                                        v-if="custom_field.value"
                                        v-for="(custom_field, key) in product.custom_fields" :key="key">
                                        <div class="panel-product__descr">
                                            <h3 class="panel-product__title">{{ custom_field.title }}</h3>
                                            <div class="panel-product__subtitle">{{ custom_field.value }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div>
                <b-tabs content-class="mt-3" class="panel-tab decs-comment">
                    <b-tab :title="translate('products.view.description')" v-if="product.description" active>
                        <p class="tab-info" v-html="product.description"></p>
                    </b-tab>
                    <b-tab :title="translate('products.view.checkout_information')" v-if="product.is_bought && product.checkout_info">
                        <p class="tab-info" v-html="product.checkout_info"></p></b-tab>
                    <!--<b-tab :title="translate('products.view.instructors')"><p class="tab-info">need descr</p></b-tab>
                        <b-tab :title="translate('products.view.courses')"><p class="tab-info">need descr</p></b-tab> -->
                    <b-tab :title="translate('products.view.reviews')" @click='heightShow'  v-if="(getSettingValue('enable_reviews') == 1) && product.enable_reviews">
                        <reviews-component
                            :click-status='activeClick'
                            :model_id="product.id"
                            :model_type="product.model"
                            :need_approval="product.enable_reviews_approval"
                            :enable_comment="getSettingValue('enable_review_comments') == 1"
                            :limit="getSettingValue('reviews_limit')"
                        ></reviews-component>
                    </b-tab>
                </b-tabs>
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
        type: {
            type: String,
            default: 'show' // show / my_show
        },
        product: {
            type: Object,
        },
    },
    data() {
        return {
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
