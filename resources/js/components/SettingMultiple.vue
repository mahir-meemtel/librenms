

<template>
    <div>
        <multiselect
        class="form-control"
            @input="$emit('input', mutateInputEvent($event))"
            :value="formattedValue"
            :required="required"
            :disabled="disabled"
            :name="name"
            label="label"
            track-by="value"
            :options="formattedOptions"
            :allow-empty="false"
            :multiple="true"
        >
        </multiselect>
    </div>
</template>

<script>
    import BaseSetting from "./BaseSetting.vue";

    export default {
        name: "SettingMultiple",
        mixins: [BaseSetting],
        computed: {
            formattedValue() {
                if (this.value === undefined) {
                    return []
                }

                let values = this.value.toString().split(',')
                return this.formatOptions(_.pick(this.options, ...values))
            },
            formattedOptions() {
                return this.formatOptions(this.options)
            }
        },
        methods: {
            formatOptions(options) {
                return Object.entries(options).map(([k, v]) => ({label: v, value: k}))
            },
            mutateInputEvent(options) {
                return options.map(option => option.value).join(',');
            }
        }
    }
</script>

<style scoped>

</style>
