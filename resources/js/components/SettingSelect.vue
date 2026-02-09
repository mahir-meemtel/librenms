

<template>
    <select class="form-control"
            :name="name"
            :value="value"
            @input="$emit('input', $event.target.value)"
            :required="required"
            :disabled="disabled"
    >
        <option v-for="(text, option) in options"
                :value="option"
                :selected="value === option"
                v-text="getText(name, text)"
        ></option>
    </select>
</template>

<script>
    import BaseSetting from "./BaseSetting.vue";

    export default {
        name: "SettingSelect",
        mixins: [BaseSetting],
        methods: {
            getText(name, text) {
                const key = `settings.settings.${name}.options.${text}`;
                return this.$te(key) ? this.$t(key) : text;
            }
        }
    }
</script>

<style scoped>

</style>
