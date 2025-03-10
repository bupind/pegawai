<script setup>
import {nextTick, onMounted, ref, watch} from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";

const props = defineProps({
    apiUrl: String,
    modelValue: [String, Number],
    placeholder: String,
    error: String,
    dependencyId: [String, Number, null],
    dependencyKey: {type: String, default: ""}
});

const emit = defineEmits(["update:modelValue"]);
const options = ref([]);
const loading = ref(false);
const initialized = ref(false);

const fetchData = async () => {
    loading.value = true;
    options.value = [];

    try {
        let url = props.apiUrl;
        if (props.dependencyId) {
            url += `?${props.dependencyKey}=${props.dependencyId}`;
        }

        const response = await axios.get(url);
        options.value = response.data.map(item => ({
            label: item.name,
            value: item.id
        }));

        await nextTick();
        if (!initialized.value && props.modelValue) {
            const exists = options.value.some(option => option.value === props.modelValue);
            if (exists) {
                emit("update:modelValue", props.modelValue);
                initialized.value = true;
            }
        }

    } catch (error) {
        options.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);

watch(() => props.dependencyId, async (newId, oldId) => {
    if (newId !== oldId) {
        await fetchData();
    }
});

watch(() => options.value, () => {
    if (options.value.length > 0 && props.modelValue) {
        const exists = options.value.some(option => option.value === props.modelValue);
        if (exists) {
            emit("update:modelValue", props.modelValue);
        }
    }
});
</script>

<template>
    <Multiselect
            :model-value="modelValue"
            :options="options"
            :placeholder="placeholder"
            :searchable="true"
            :clearable="true"
            :disabled="loading || !options.length"
            :classes="{option: 'hover:bg-gray-200 dark:hover:bg-slate-700 bg-white dark:bg-slate-800 px-3 py-2'}" class="bg-white dark:bg-slate-800"
            @update:model-value="$emit('update:modelValue', $event)"
    />
</template>

<style src="@vueform/multiselect/themes/default.css"></style>
