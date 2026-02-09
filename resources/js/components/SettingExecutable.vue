

<template>
        <input type="text" class="form-control"
               :name="name"
               :value="value"
               @input="$emit('input', $event.target.value)"
               :pattern="pattern"
               :required="required"
               :disabled="disabled"
        >
</template>

<script>
    import BaseSetting from "./BaseSetting.vue";

    export default {
        name: "SettingExecutable",
        mixins: [BaseSetting]
    }
</script>

<style scoped>

</style>
