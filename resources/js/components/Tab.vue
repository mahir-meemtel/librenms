
<template>
    <div v-show="isActive" role="tabpanel" class="tab-pane" :id="name">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "Tab",
        props: {
            name: { required: true },
            text: String,
            selected: {type: Boolean, default: false},
            icon: String
        },
        data() {
            return {
                isActive: this.selected
            }
        }
    }
</script>

<style scoped>

</style>
