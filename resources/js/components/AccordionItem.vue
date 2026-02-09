

<template>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" :id="slug()">
            <h4 class="panel-title">
                <a class="accordion-item-trigger" :class="{'collapsed': !isActive}" role="button" data-parent="#accordion" @click="isActive = !isActive" :data-href="hash()">
                    <i class="fa fa-chevron-down accordion-item-trigger-icon"></i>
                    <i v-if="icon" :class="['fa', 'fa-fw', icon]"></i>
                    {{ text || name  }}
                </a>
            </h4>
        </div>
        <transition-collapse-height>
            <div :id="slug() + '-content'" v-if="isActive" :class="['panel-collapse', 'collapse', {'in': isActive}]" role="tabpanel">
                <div class="panel-body">
                    <slot></slot>
                </div>
            </div>
        </transition-collapse-height>
    </div>
</template>

<script>
    export default {
        name: "AccordionItem",
        props: {
            name: {
                type: String,
                required: true
            },
            text: String,
            active: Boolean,
            icon: String
        },
        data() {
            return {
                isActive: this.active
            }
        },
        mounted() {
            if (window.location.hash === this.hash()) {
                this.isActive = true;
            }
        },
        watch: {
            active(active) {
                this.isActive = active;
            },
            isActive(active) {
                this.$parent.$emit(active ? 'expanded' : 'collapsed', this.slug())
            }
        },
        methods: {
            slug() {
                return this.name.toString().toLowerCase().replace(/\s+/g, '-');
            },
            hash() {
                return '#' + this.slug();
            }
        }
    }
</script>

<style scoped>
    .accordion-item-trigger-icon {
        transition: transform 0.2s ease;
    }
    .accordion-item-trigger.collapsed .accordion-item-trigger-icon {
        transform: rotate(-90deg);
    }
</style>
