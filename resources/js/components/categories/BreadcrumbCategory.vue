<template>
    <div class="content-header">
        <div class="category-breadcrumb">
            <h2>
                <router-link :to="'/admin/categories'">
                    {{translate('categories.categories')}}
                </router-link>

                <span v-if="parentCategories">
                    <span v-for="(pCategory, key) in parentCategories" :key="key">
                        <span> / </span>
                        <router-link v-if="model" :to="'/admin/categories' + (pCategory.id ? '/' + pCategory.id : '')">
                            {{ pCategory.name }}
                        </router-link>
                    </span>
                </span>

                <span v-if="model">
                    <span> / </span>
                    {{model.name}}
                </span>
            </h2>
        </div>

        <div class="d-flex">
            <!-- <router-link v-if="model" :to="'/admin/categories' + (model.parent_id ? '/' + model.parent_id : '')" class="btn back-button">
                {{translate('system.back')}}
            </router-link> -->

            <div class="col-auto pl-0"></div>

            <router-link :to="{name: 'admin-categories-create', params: { parentId: model ? model.id : null }}" class="btn product-button-active">
                {{translate('system.create_new')}}
            </router-link>
        </div>
    </div>
</template>

<script>
import '@sass/component/category/breadcrumb.scss';
import '@sass/component/product-btns.scss'
export default {
    props: {
        model: {
            type: Object,
        }
    },
    data: function () {
        return {
            parentCategories: null
        }
    },
    mounted() {
        let app = this;
    },
    watch: {
        model: function () {
            let app = this;
            app.getParentCategories();
        }
    },
    methods: {
        getParentCategories() {
            let app = this;
            if (app.model) {
                axios.get('/admin/categories/get-parent-categories/' + app.model.id).then(response => {
                    app.parentCategories = response.data;
                }).catch(function (resp) {
                    console.error(resp);
                });
            } else {
                app.parentCategories = null;
            }
        }
    }
}
</script>
