<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate(translatePath + '.roles')}}
            </h2>
        </div>
        <div class="content-table">
            <div class="panel">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table-grid :gridData="modelsData.gridData" :data="modelsData" @get-models="getModels"></table-grid>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        let app = this;
        return {
            translatePath: 'roles',
            modelsData: {}
        }
    },
    mounted() {
        this.getModels();
    },
    methods: {
        getModels(page, sort = null) {
            let app = this;
            if (typeof page === 'undefined') {
                page = 1;
            }

            let params = {
                page: page,
                sort: sort
            };

            axios.get('/admin/roles', {params: params}).then(response => {
                app.modelsData = response.data;

            }).catch(function (resp) {
                console.error(resp);
            });
        },
    }
}
</script>