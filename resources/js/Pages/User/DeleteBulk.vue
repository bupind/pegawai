<script setup>
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import {ref, watchEffect} from "vue";
import {TrashIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["close"]);
const show = ref(false);
const props = defineProps({
    title: String,
    selectedId: Object,
});

const form = useForm({
    id: [],
});

watchEffect(() => {
    if (show) {
        form.id = props.selectedId;
    }
});

const submit = () => {
    form.post(route("user.destroy-bulk"), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            emit("close");
        },
        onError: () => null,
        onFinish: () => null,
    });
};

const closeModal = () => {
    show.value = false;
};
</script>
<template>
    <div>
        <DangerButton
                v-tooltip="lang().label.delete_selected"
                class="rounded-none"
                @click.prevent="show = true"
        >
            <TrashIcon class="w-4 h-auto"/>
        </DangerButton>
        <ConfirmationModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.delete_selected }} {{ props.title }}
            </template>

            <template #content>
                {{ lang().label.delete_confirm }}
                {{ props.selectedId?.length }} {{ props.title }}?
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    {{ lang().button.cancel }}
                </SecondaryButton>

                <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="ml-3"
                        @click="submit"
                >
                    {{ lang().button.delete }}
                    {{ form.processing ? "..." : "" }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
