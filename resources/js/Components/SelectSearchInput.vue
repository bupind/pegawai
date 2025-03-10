<script setup>
import {defineEmits, defineProps, ref, watch} from "vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

const props = defineProps({
    modelValue: [String, Number, Array],
    dataSet: {
        type: Array,
        default: () => [],
    },
    error: {
        type: String,
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: "Pilih opsi",
    },
});

const emit = defineEmits(["update:modelValue"]);
const selectedValue = ref(props.modelValue ?? (props.multiple ? [] : null));
watch(selectedValue, (newValue) => {
    emit("update:modelValue", newValue);
});

watch(() => props.modelValue, (newValue) => {
    selectedValue.value = newValue ?? (props.multiple ? [] : null);
}, {immediate: true});
</script>

<template>
    <Multiselect
            v-model="selectedValue"
            :options="dataSet"
            :searchable="true"
            :multiple="multiple"
            :placeholder="placeholder"
            track-by="value"
            label="label"
            class="custom-multiselect rounded shadow-sm placeholder:text-slate-300 dark:placeholder:text-slate-700 w-full"
            :class="{ 'border-red-500': error }"
    />
</template>

<style scoped>
.custom-multiselect {
    border-color: #6b7280;
    border-width: 1px;
    font-size: 1rem;
    line-height: 1.5rem;
    padding: 0px;
}

.custom-multiselect .multiselect-tags {
    min-height: 48px;
}

.custom-multiselect .multiselect-input {
    min-height: 26px;
    font-size: 12px;
    padding: 4px 6px;
}

.custom-multiselect .multiselect-dropdown {
    font-size: 12px;
}

.custom-multiselect .multiselect-option {
    padding: 4px 6px;
}
</style>
