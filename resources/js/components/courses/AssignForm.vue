<template>
    <div>
        <form v-on:submit="saveForm()">
            
            <div class="row" v-if="mSelect">
                <div class="col">
                    <label for="courses" class="assign-label">{{translate('courses.form.select_courses')}}</label>
                    <custom-multiselect @update="assign.course_id = $event;" :searchData="mSelect.course" :selectedItems="assign.course_id"
                        :config="courceSelectConfig" ref="multiselect"></custom-multiselect>
                </div>
                <div class="col">
                    <label for="categories" class="assign-label">{{translate('courses.form.select_categories')}}</label>
                    <custom-multiselect @update="assign.category_id = $event; $refs.multiselect.selectedItem=[]" :searchData="mSelect.category" :selectedItems="assign.category_id"
                        :config="categorySelectConfig" ref="multiselect_category"></custom-multiselect>
                </div>
            </div>
            
            <button class="btn btn-primary float-right assign-form-btn">{{translate('courses.assign')}}</button>
        </form>
    </div>
</template>

<script>
import CustomMultiselect from '@component/form/CustomMultiselect.vue';
import '../../../sass/component/admin-area.scss'
export default {
    components: {
        CustomMultiselect
    },
    props: ['product'],
    data: function () {
        let app = this;
        return {
            assign: {
                product_id: 0,
                course_id: [],
                category_id: 0
            },
            courceSelectConfig: {
                multiple: true,
                searchAjax: true,
                selectAll: {
                    show: true,
                    title: app.translate('courses.form.select_all_courses')
                },
            },
            categorySelectConfig: {
                multiple: false,
                searchAjax: true,
                closeOnSelect: true, 
            }
        }
    },
    computed: {
        mSelect: function() {
            let app = this;
            return {
                course: {
                    url: '/admin/product-assign-course/get-courses',
                    params: {
                        product_id: app.product.id,
                        category_id: app.assign.category_id,
                    }
                },
                category: {
                    url: '/admin/product-assign-course/get-categories',
                },
            }
        }
    },
    methods: {
        clearForm() {
            let app = this;
            app.$refs.multiselect.clearAll();
            app.$refs.multiselect_category.clearAll();
        },
        saveForm() {
            event.preventDefault();
            let app = this;
            app.assign.product_id = app.$route.params.id;
            app.$emit('save-form', app.assign);
        }
    }
}
</script>