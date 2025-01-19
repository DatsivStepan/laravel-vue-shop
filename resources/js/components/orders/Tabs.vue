<template>
    <ul id="stepFormProgress" class="js-step-progress list-inline u-shopping-cart-step-form" :class="tabs.length === 2 ? 'only-two': ''">
        <li class="list-inline-item u-shopping-cart-step-form__item"
            v-for="(tab, key) in tabs" :key="tab.contentKey" :style="{ width: (100 / tabs.length) + '%'}"
            :class="{ active: cartActive == tab.contentKey}">
            <div class="d-inline-block">
                <span class="tab-icon-order" :class="iconData[key]"></span>
                <span class="u-shopping-cart-step-form__title">{{tab.title}}</span>
            </div>
        </li>
    </ul>
</template>
<script>
export default {
    props: {
        tabs: {
            type: Array,
        },
    },
    data() {
        return {
            iconData: ['icon-product-data', 'icon-consumer-info','icon-consumer-info',  'icon-options', 'icon-payment'],
            arrowArray: [ null,  'chevron-forward', 'chevron-forward','chevron-forward','chevron-forward','chevron-forward'],
        }
    },
    computed: {
        cartActive: {
            get: function () {
                return this.$store.state.cartTabActive != null ? this.$store.state.cartTabActive : this.tabs[0].contentKey;
            }
        }
    },
}
</script>
