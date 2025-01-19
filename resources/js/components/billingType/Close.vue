<template>

</template>

<script>
export default {
    name: "Close",
    mounted: function () {
        if (this.$route.query.close) {
            window.close()
        }
    },
}
</script>

<style scoped>

</style>
