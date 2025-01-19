<template>
    <div>
        <div class="content-header">
            <h2>
              {{ translate(translatePath + '.payments') }}
            </h2>
            <router-link :to="{name: 'admin-payments-create'}" class="btn float-right mb-2 product-button-active">
              {{ translate(translatePath + '.create_new') }}
            </router-link>
        </div>

        <grid-item :items="items" @get-models="getModels"></grid-item>
    </div>
</template>

<script>
import GridItem from '../../components/GridItem.vue';
import '@sass/component/product-btns.scss'

export default {
    components: {
      GridItem,
    },
    data: function () {
        let app = this;
        return {
            translatePath: 'payments',
            items: {},
        }
    },
    mounted() {
        this.getModels();
    },
    methods: {
        getModels(page = 1, sort = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }
            let params = {
                page: page,
                sort: sort
            };

            axios.get('/admin/payments', {params: params}).then(response => {
                app.items = response.data.data;

            }).catch(function (resp) {
                console.log(resp);
            });
        },
    }
}
</script>
