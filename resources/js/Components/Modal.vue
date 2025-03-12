<script setup>
import {computed, onMounted, onUnmounted, watch} from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: "lg",
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close"]);

watch(
    () => props.show,
    () => {
        document.body.style.overflow = props.show ? "hidden" : null;
    }
);

const close = () => {
    if (props.closeable) {
        emit("close");
    }
};

const closeOnEscape = (e) => {
    if (e.key === "Escape" && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener("keydown", closeOnEscape));
onUnmounted(() => {
    document.removeEventListener("keydown", closeOnEscape);
    document.body.style.overflow = null;
});
const maxWidthClass = computed(() => ({
    sm: "max-w-sm",
    md: "max-w-md",
    lg: "max-w-lg",
    xl: "max-w-xl",
    "2xl": "max-w-2xl",
    "3xl": "max-w-3xl",
    "4xl": "max-w-4xl",
    "5xl": "max-w-5xl",
    "6xl": "max-w-6xl",
    "7xl": "max-w-7xl",
    full: "w-full h-full max-w-none",
}[props.maxWidth] || "4xl"));
</script>

<template>
    <teleport to="body">
        <div v-show="show" class="fixed inset-0 flex items-center justify-center px-4 py-6 sm:px-0 z-50">
            <div class="fixed inset-0 bg-slate-500 dark:bg-slate-900 opacity-75" @click="close"></div>
            <div v-show="show"
                 class="bg-white dark:bg-slate-800 rounded shadow text-gray-700 dark:text-white transform transition-all sm:w-full sm:mx-auto"
                 :class="maxWidthClass"
            >
                <slot v-if="show"/>
            </div>
        </div>
    </teleport>
</template>
