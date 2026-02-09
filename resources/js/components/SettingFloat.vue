

<template>
    <input type="number" class="form-control"
           :name="name"
           :value="value"
           @input="$emit('input', parseNumber($event.target.value))"
           :required="required"
           :disabled="disabled"
    >
</template>

<script>
import BaseSetting from "./BaseSetting.vue";

export default {
        name: "SettingFloat",
        mixins: [BaseSetting],
        methods: {
            parseNumber(number) {
                let value = parseFloat(number);
                return isNaN(value) ? number : value;
            }
        }
    }
</script>

<style scoped>
    .form-control {
        padding-right: 12px;
    }
</style>
