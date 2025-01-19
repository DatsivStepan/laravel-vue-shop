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
            },
            status: {
                type: Boolean,
                default: true
            }
        },
        mounted() {
            this.$root.$on('bv::dropdown::show', bvEvent => {
                this.$emit('dropdown-show', this.status)
            })
            this.$root.$on('bv::dropdown::hide', bvEvent => {
                this.$emit('dropdown-hide', !this.status)
            })
        },
    };
</script>

