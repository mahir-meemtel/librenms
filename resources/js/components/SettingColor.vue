

<template>
    <input type="color" class="form-control"
           :name="name"
           :value="value"
           @input="$emit('input', $event.target.value)"
           :required="required"
           :disabled="disabled"
    >
</template>

<script>
import BaseSetting from "./BaseSetting.vue";

export default {
    name: "SettingColor",
    mixins: [BaseSetting],
}
</script>

<style scoped>
.form-control {
    padding-right: 12px;
}
</style>
