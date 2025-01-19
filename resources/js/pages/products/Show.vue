<template>
    <component
        v-if="product && themeComponent"
        :is="themeComponent"
        :product="product"
        :type="type"
    ></component>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'show' // show / my_show
        },
    },
    data() {
        console.log('page product show')
        console.log(this.type)
        return {
            theme: null,
            product: null,
            pagePath: '/pages/products/Show'
        }
    },
    computed: {
        themeComponent: {
            get: function () {
                let app = this;
                if (app.theme) {
                    return () => import(`@themes/` + app.theme + app.pagePath)
                        .then((m) => m.default)
                        .catch(() => import(`@themes/default` + app.pagePath));
                }
            }
        }
    },
    mounted() {
        this.getProductData();
    },
    methods: {
        getProductData() {
            let app = this;
            let id = app.$route.params.id;

            axios.get('/products/' + id)
                .then(function (resp) {
                    app.product = resp.data.data;
                    if (app.product.theme_key) {
                        app.theme = app.product.theme_key;
                    } else {
                        app.theme = app.getSettingValue('theme');
                    }
                })
                .catch(function () {
                    console.log("error get product id:" + id);
                });
        },
    }
}
</script>
