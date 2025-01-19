<template>
    <b-dropdown class="drop-down-caret" variant="link" no-caret dropleft>
        <template v-slot:button-content>
            <ion-icon :name="'ellipsis-' + dimension"></ion-icon>
        </template>
        <slot/>
    </b-dropdown>
</template>
<script>
export default {
    props: {
        dimension: {
            type: String,
            default: 'horizontal'
        }
    }
};
</script>

