<template>

    <div>
        <select class="form-control" v-model='paginationSize' @change="changePaginationSize()">
            <option v-for="size in sizes" :value="size">
                {{ size }}
            </option>
        </select>
    </div>

</template>

<script>
export default {
    props: {
        meta: {
            type: Object,
        },
    },
    data: function () {
        return {
            sizes: [
                10, 20, 50, 100, 200, 500, 1000 
            ],
            paginationSize: 0
        }
    },
    mounted() {
        let app = this;
        app.paginationSize = app.meta.per_page;
    },
    methods: {
        changePaginationSize() {
            let app = this;
            app.$emit('change-pagination-size', app.paginationSize);
        },
    },
}
</script>
