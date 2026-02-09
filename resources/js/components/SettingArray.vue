
<template>
    <div v-tooltip="disabled ? $t('settings.readonly') : false">
        <draggable v-model="localList" @end="dragged()" :disabled="disabled">
            <div v-for="(item, index) in localList" class="input-group">
                <span :class="['input-group-addon', disabled ? 'disabled' : '']">{{ index+1 }}.</span>
                <input type="text"
                       class="form-control"
                       :value="item"
                       :readonly="disabled"
                       @blur="updateItem(index, $event.target.value)"
                       @keyup.enter="updateItem(index, $event.target.value)"
                >
                <span class="input-group-btn">
                    <button v-if="!disabled" @click="removeItem(index)" type="button" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </span>
            </div>
        </draggable>
        <div v-if="!disabled">
            <div class="input-group">
                <input type="text" v-model="newItem" @keyup.enter="addItem" class="form-control">
                <span class="input-group-btn">
                    <button @click="addItem" type="button" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import BaseSetting from "./BaseSetting.vue";
    import draggable from 'vuedraggable'

    export default {
        name: "SettingArray",
        mixins: [BaseSetting],
        components: {
            draggable,
        },
        data() {
            return {
                localList: this.value ?? [],
                newItem: ""
            }
        },
        methods: {
            addItem() {
                if (this.disabled) return;
                this.localList.push(this.newItem);
                this.$emit('input', this.localList);
                this.newItem = "";
            },
            removeItem(index) {
                if (this.disabled) return;
                this.localList.splice(index, 1);
                this.$emit('input', this.localList);
            },
            updateItem(index, value) {
                if (this.disabled || this.localList[index] === value) return;
                this.localList[index] = value;
                this.$emit('input', this.localList);
            },
            dragged() {
                if (this.disabled) return;
                this.$emit('input', this.localList);
            }
        },
        watch: {
            value(updated) {
                // careful to avoid loops with this
                this.localList = updated;
            }
        }
    }
</script>

<style scoped>
    .input-group {
        margin-bottom: 3px;
    }

    .input-group-addon:not(.disabled) {
        cursor: move;
    }
</style>
