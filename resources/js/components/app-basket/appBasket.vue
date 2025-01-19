<template>
    <div class="app-basket-folder">
        <ion-icon name="basket" class="icon"></ion-icon>
        <span class="basket-count">{{basketCount}}</span>
    </div>
</template>
<script>
    export default {
        props: {
            basketCount: {
                default: '0'
            }
        }
    }
</script>
