

<template>
    <div>Invalid type for: {{ name }}</div>
</template>

<script>
    export default {
        name: "SettingNull",
        props: ['name']
    }
</script>

<style scoped>
    div {
        color: red;
    }
</style>
