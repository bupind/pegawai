<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import ActionButton from "@/Components/ActionButton.vue";
import { useForm } from "@inertiajs/vue3";
import { onUpdated, ref } from "vue";
import { EyeIcon } from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);

const props = defineProps({
    title: String,
    employee: Object,
    statuses: Object,
    genders: Object,
});

const form = useForm({
    code: "",
    name: "",
    gender: "",
    status: "",
});

onUpdated(() => {
    if (show.value && props.employee) {
        form.code = props.employee.code ?? "";
        form.name = props.employee.name ?? "";
        form.gender = props.employee.gender ?? "";
        form.status = props.employee.status ?? "";
    }
});

const closeModal = () => {
    show.value = false;
    form.errors = {};
    form.reset();
};
</script>

<template>
    <div>
        <ActionButton v-tooltip="lang().label.read" @click.prevent="(show = true), emit('open')">
            <EyeIcon class="w-4 h-auto" />
        </ActionButton>

        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.read }} {{ props.title }}
            </template>
            <template #content>
                <div class="space-y-4">
                    <div>
                        <InputLabel :value="lang().label.Code" />
                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ form.code || '-' }}</p>
                    </div>

                    <div>
                        <InputLabel :value="lang().label.Employee" />
                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ form.name || '-' }}</p>
                    </div>

                    <div>
                        <InputLabel :value="lang().label.Gender" />
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            {{ props.genders[form.gender] || '-' }}
                        </p>
                    </div>

                    <div>
                        <InputLabel :value="lang().label.Status" />
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            {{ props.statuses[form.status] || '-' }}
                        </p>
                    </div>
                </div>
            </template>
        </DialogModal>
    </div>
</template>
