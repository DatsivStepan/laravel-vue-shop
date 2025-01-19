<template>
    <div class="form-group" v-if='config' :class="{'required': config.required }">

        <label class="form-control-label" :class="{'text-danger': config.errors }"
               :for="config.for">{{ config.label }}</label>

        <div>

            <slot></slot>

            <div class="valid-feedback">{{ config.success }}</div>
            <div class="invalid-feedback" v-if='config.errors'>
                <ul>
                    <li v-for="error in config.errors">
                        {{ error }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    props: ['config']
}
</script>
