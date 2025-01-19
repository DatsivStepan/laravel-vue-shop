<template>
    <router-link  class="app-favorite-folder" :to="{ name : 'my-favorites' }">
        <ion-icon name="heart" class="icon"></ion-icon>
        <span class="favorite-count">{{favoriteCount}}</span>
    </router-link>
</template>
<script>
export default {
    props: {
        favoriteCount: {
            default: '0'
        }
    }
}
</script>
