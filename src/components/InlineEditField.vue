<template>
    <span v-if="!isEditing" @click="handleLabelClick()">{{ fieldValueData }}</span>
    <span v-else><input type="text" ref="field" :value="fieldValueData" @blur="handleBlur()" @keyup.esc="handleEsc()" @keyup.enter.prevent="handleEnter()" /></span>
</template>
<script>

export default {
    components:{

    },
    emits: ['fieldUpdated'],
    props: {
        fieldValue: {
            type: [Number, String],
            default: ''
        },
        className: {
            type: String,
            default: null
        },
        fieldType: {
            type: String,
            default: 'shortText'
        }
    },
    data() {
        return {
            fieldValueData: this.$_lodash.cloneDeep(this.$props.fieldValue), 
            isEditing: false
        }
    },
    methods: {
        handleEsc() {
            this.isEditing = false;
        },
        handleEnter() {
            this.fieldChanged();
        },
        handleLabelClick() {
            this.isEditing = true;
            //must use 'nextTick' to wait for next DOM update cycle (otherwise input field won't exist in dom yet)
            this.$nextTick(() => this.$refs['field'].focus())
        },
        handleBlur() {
            this.fieldChanged();
            
        },
        fieldChanged() {
            //check that we are still editing (in case we have 'Enter' event and then 'Blur' event both firing)
            if(this.isEditing) {
                this.fieldValueData = this.$refs['field'].value;
                this.$emit('fieldUpdated', this.fieldValueData);
                this.isEditing=false;
            }
        }
        
    }

}
</script>
<style scoped>

</style>