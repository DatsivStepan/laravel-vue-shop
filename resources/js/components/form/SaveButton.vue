<template>
    <button @click="scrolltoTop" class="btn btn-primary float-right setting-btn">{{translate('settings.savechanges')}}</button>
</template>

<script>
export default {
    name: 'savebtn',
    methods: {
        scrolltoTop(){
            setTimeout(()=>{
                window.scrollTo({
                top: 0,
                behavior: "smooth"
                });
            },1000)
        }
    }
}
</script>