<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
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
    license: Object,
});

const form = useForm({
    employeeId: "",
    type: "",
    registrationNumber: "",
    validFrom: "",
    validUntil: "",
    status: "",
});
onUpdated(() => {
    if (show) {
        form.employeeId = props.license?.employee.name;
        form.type = props.license?.employee?.type;
        form.registrationNumber = props.license?.registrationNumber;
        form.validFrom = props.license?.validFrom;
        form.validUntil = props.license?.validUntil;
        form.status = props.license?.calculatedStatus;
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
        <ActionButton
                v-tooltip="lang().label.read"
                @click.prevent="(show = true), emit('open')"
        >
            <EyeIcon class="w-4 h-auto"/>
        </ActionButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.read }} {{ props.title }}
            </template>

            <template #content>
                <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-0">
                    <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-4 font-medium w-1/3">{{ lang().label.employee }}</th>
                        <td class="py-2 px-4">{{ form.employeeId || "-" }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-4 font-medium">{{ lang().label.type }}</th>
                        <td class="py-2 px-4">{{ props.types[form.type] || "-" }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-4 font-medium">{{ lang().label.registrationNumber }}</th>
                        <td class="py-2 px-4">{{ form.registrationNumber || "-" }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-4 font-medium">{{ lang().label.validFrom }}</th>
                        <td class="py-2 px-4">{{ form.validFrom || "-" }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-4 font-medium">{{ lang().label.validUntil }}</th>
                        <td class="py-2 px-4">{{ form.validUntil || "-" }}</td>
                    </tr>
                    <tr>
                        <th class="py-2 px-4 font-medium">{{ lang().label.status }}</th>
                        <td class="py-2 px-4">{{ props.statuses[form.status] || "-" }}</td>
                    </tr>
                    </tbody>
                </table>
            </template>

        </DialogModal>
    </div>
</template>
