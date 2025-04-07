<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DropdownLoader from "@/Components/DropdownLoader.vue";
import ActionButton from "@/Components/ActionButton.vue";
import {useForm} from "@inertiajs/vue3";
import {onUpdated, ref} from "vue";
import {EyeIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);
const props = defineProps({
    title: String,
    statuses: Object,
    types: Object,
    registrationcertificate: Object,
});

const form = useForm({
    employeeId: "",
    type: "",
    registrationNumber: "",
    competence: "",
    validFrom: "",
    validUntil: "",
    status: "",
});
onUpdated(() => {
    if (show) {
        form.employeeId = props.registrationcertificate?.employee.name;
        form.type = props.registrationcertificate?.type;
        form.registrationNumber = props.registrationcertificate?.registrationNumber;
        form.competence = props.registrationcertificate?.competence;
        form.validFrom = props.registrationcertificate?.validFrom;
        form.validUntil = props.registrationcertificate?.validUntil;
        form.status = props.registrationcertificate?.status;
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
            <EyeIcon class="w-4 h-auto"/>
        </ActionButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.read }} {{ props.title }}
            </template>

            <template #content>

                <div class="space-y-1">
                    <InputLabel :value="lang().label.Employee" for="employeeId"/>
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ form.employeeId || "-" }}
                    </p>
                </div>
                <div class="space-y-1">
                    <InputLabel for="type" :value="lang().label.type"/>
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ props.types[form.type] || "-" }}
                    </p>
                </div>
                <div>
                    <InputLabel :value="lang().label.registrationNumber"/>
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ form.registrationNumber || "-" }}
                    </p>
                </div>

                <div>
                    <InputLabel :value="lang().label.competence"/>
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ form.competence || "-" }}
                    </p>
                </div>
                <div class="flex gap-4">
                    <div class="w-1/2">
                        <InputLabel :value="lang().label.validFrom"/>
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            {{ form.validFrom || "-" }}
                        </p>
                    </div>
                    <div class="w-1/2">
                        <InputLabel :value="lang().label.validUntil"/>
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            {{ form.validUntil || "-" }}
                        </p>
                    </div>
                </div>
                <div class="space-y-1">
                    <InputLabel for="status" :value="lang().label.status"/>
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ props.statuses[form.status] || "-" }}
                    </p>
                </div>

            </template>
        </DialogModal>
    </div>
</template>
