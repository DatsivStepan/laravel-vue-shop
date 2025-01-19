<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate('orders.invoices')}}
            </h2>
        </div>

        <div class="card invoice-card" style="margin-top: 20px">
            <div class="card-body">
                <component :is="'sales-grid'" url="/admin/invoices"></component>
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
    }
}
</script>
