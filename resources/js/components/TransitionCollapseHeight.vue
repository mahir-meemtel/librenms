

<template>
    <transition
        enter-active-class="enter-active"
        leave-active-class="leave-active"
        @before-enter="beforeEnter"
        @enter="enter"
        @after-enter="afterEnter"
        @before-leave="beforeLeave"
        @leave="leave"
        @after-leave="afterLeave"
    >
        <slot />
    </transition>
</template>

<script>
    export default {
        name: "TransitionCollapseHeight",
        methods: {
            beforeEnter(el) {
                requestAnimationFrame(() => {
                    if (!el.style.height) {
                        el.style.height = '0px';
                    }

                    el.style.display = null;
                });
            },
            enter(el) {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        el.style.height = el.scrollHeight + 'px';
                    });
                });
            },
            afterEnter(el) {
                el.style.height = null;
            },
            beforeLeave(el) {
                requestAnimationFrame(() => {
                    if (!el.style.height) {
                        el.style.height = el.offsetHeight + 'px';
                    }
                });
            },
            leave(el) {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        el.style.height = '0px';
                    });
                });
            },
            afterLeave(el) {
                el.style.height = null;
            }
        }
    }
</script>

<style scoped>
    .enter-active,
    .leave-active {
        overflow: hidden;
        transition: height 0.2s linear;
    }
</style>
