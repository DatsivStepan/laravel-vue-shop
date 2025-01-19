<template>
    <div>
        <table-grid v-bind:gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
    </div>
</template>

<script>
export default {
    props: ['instanceType', 'instanceId'],
    data: function () {
        let app = this;
        return {
            modelsData: {},
        }
    },
    mounted() {
        this.getModels();
    },
    watch: { 
        instanceId: function(newVal, oldVal) { // watch it
            this.getModels();
        }
    },
    methods: {
        getModels(page = 1, sort = null, filters = null, perPage = null) {
            let app = this;
            if (app.instanceId == 0) {
                return;
            }

            if (typeof page === 'undefined') {
                page = 1;
            }

            let params = {
                page: page,
                sort: sort,
                instanceType: app.instanceType,
                instanceId: app.instanceId,
                perPage: perPage,
                filters: filters
            };

            axios.get('/admin/vendors-assign', {params: params}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.log(resp);
            });
        },
    }
}
</script>