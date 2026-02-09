<!--
  - Accordian.vue
  -
  - Accordion component contains multiple
  -->

<template>
    <div class="panel-group" role="tablist">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "Accordion",
        props: {
            multiple: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            setActive(name) {
                this.$children.forEach((item) => {
                    if (item.slug() === name) {
                        item.isActive = true;
                    }
                })
            },
            activeChanged(name) {
                if (!this.multiple) {
                    this.$children.forEach((item)=> {
                        if (item.slug() !== name) {
                            item.isActive = false
                        }
                    })
                }
            }
        },
        mounted() {
            this.$on('expanded', this.activeChanged);
        }
    }
</script>

<style scoped>

</style>
