<template>
    <div>
        <div class="content-header clearfix">
            <h2>
                {{translate('orders.shipping')}}
            </h2>
        </div>

        <div class="card shiping-card" style="margin-top: 20px">
            <div class="card-body">
                <component :is="'sales-grid'" url="/admin/shipping"></component>
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
