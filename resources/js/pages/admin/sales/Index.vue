<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate('orders.all_sales')}}
            </h2>
        </div>

        <div class="card assign-body assign-secondary assign-all-sales" style="margin-top: 20px">
            <div class="card-body assing-inner">
                <component :is="'sales-grid'" url="/admin/sales" :isSales="isAllSales"></component>
            </div>
        </div>
    </div>
</template>

<script>
import SalesGrid from './SalesGrid';
import '@sass/component/admin-area.scss'

export default {
    components: {
        SalesGrid
    },
    data: function(){
        return {
            isAllSales : true
        }
    },
    methods: {
        getCard(){
            const card = document.querySelector()
        }
    }
}
</script>
