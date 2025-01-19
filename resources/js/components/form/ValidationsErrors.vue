<template>
    <div v-if="errors">
        <ul class="alert alert-danger">
            <template v-for="(errorGroup, key) in errors">
                <li v-for="(error, key) in errorGroup">{{ error }}</li>
            </template>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['errors']
}
</script>