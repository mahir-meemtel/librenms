
<template>
    <div>
        <obzora-select class="form-control"
                :value="value"
                :route-name="'ajax.select.' + this.options.target"
                :placeholder="this.options.placeholder"
                :allow-clear="this.options.allowClear"
                :required="required"
                :disabled="disabled"
                @change="$emit('change', $event)"
        >
        </obzora-select>
    </div>
</template>

<script>
import BaseSetting from "./BaseSetting.vue";
import ObzoraSelect from "./ObzoraSelect.vue";

export default {
    name: "SettingSelectDynamic",
    components: {ObzoraSelect},
    mixins: [BaseSetting]
}
</script>

<style scoped>

</style>
