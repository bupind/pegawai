<script setup>
import {ref} from "vue";
import {ArrowsPointingInIcon, ArrowsPointingOutIcon, XMarkIcon} from "@heroicons/vue/24/outline";
import Modal from "./Modal.vue";

const emit = defineEmits(["close"]);
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: "2xl",
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});
const isFullscreen = ref(false);
const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
};
const close = () => {
    if (props.closeable) {
        isFullscreen.value = false;
        emit("close");
    }
};
</script>

<template>
    <Modal :show="show" :max-width="isFullscreen ? 'full' : maxWidth" :closeable="closeable" @close="close">
        <div class="px-2 py-2 border-b-2 rounded-tl rounded-tr flex justify-between items-center">
            <slot name="title"/>
            <div class="flex space-x-2">
                <button @click="toggleFullscreen"
                        class="p-2 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <ArrowsPointingOutIcon v-if="!isFullscreen" class="w-5 h-5 text-gray-700 dark:text-white"/>
                    <ArrowsPointingInIcon v-else class="w-5 h-5 text-gray-700 dark:text-white"/>
                </button>
                <button @click.prevent="close"
                        class="p-2 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <XMarkIcon class="w-5 h-auto"/>
                </button>
            </div>
        </div>

        <div class="p-4">
            <slot name="content"/>
        </div>

        <div class="flex flex-row justify-end px-6 py-4 bg-slate-100 dark:bg-slate-900/30 text-right rounded-bl rounded-br">
            <slot name="footer"/>
        </div>
    </Modal>
</template>
