

<template>
    <toggle-button
        :name="name"
        :value="value"
        @change="$emit('change', $event.value)"
        :sync="true"
        :required="required"
        :disabled="disabled"
    ></toggle-button>
</template>

<script>
    import BaseSetting from "./BaseSetting.vue";

    export default {
        name: "SettingBoolean",
        mixins: [BaseSetting]
    }
</script>

<style scoped>

</style>
