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
    employeeType: "",
    registrationNumber: "",
    competence: "",
    validFrom: "",
    validUntil: "",
    status: "",
});
onUpdated(() => {
    if (show) {
        form.employeeId = props.registrationcertificate?.employee.name;
        form.employeeType = props.registrationcertificate?.employee.type;
        form.registrationNumber = props.registrationcertificate?.registrationNumber;
        form.competence = props.registrationcertificate?.competence;
        form.validFrom = props.registrationcertificate?.validFrom;
        form.validUntil = props.registrationcertificate?.validUntil;
        form.status = props.registrationcertificate?.calculatedStatus;
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
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-800 dark:text-gray-200 border">
                        <tbody>
                        <tr class="border-b">
                            <th class="px-4 py-2 w-1/3 font-medium">{{ lang().label.employee }}</th>
                            <td class="px-4 py-2">{{ form.employeeId || "-" }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 w-1/3 font-medium">{{ lang().label.type }}</th>
                            <td class="px-4 py-2">{{ props.types[form.employeeType] || "-" }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.registrationNumber }}</th>
                            <td class="px-4 py-2">{{ form.registrationNumber || "-" }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.competence }}</th>
                            <td class="px-4 py-2">{{ form.competence || "-" }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.validFrom }}</th>
                            <td class="px-4 py-2">{{ form.validFrom || "-" }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.validUntil }}</th>
                            <td class="px-4 py-2">{{ form.validUntil || "-" }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 font-medium">{{ lang().label.status }}</th>
                            <td class="px-4 py-2">{{ props.statuses[form.status] || "-" }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </template>

        </DialogModal>
    </div>
</template>
