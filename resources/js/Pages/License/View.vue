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
        form.type = props.license?.type;
        form.registrationNumber = props.license?.registrationNumber;
        form.validFrom = props.license?.validFrom;
        form.validUntil = props.license?.validUntil;
        form.status = props.license?.status;
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
                <div class="space-y-1">
                    <InputLabel :value="lang().label.employee" for="employeeId"/>
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
                <div class="space-y-1">
                    <InputLabel for="registrationNumber" :value="lang().label.registrationNumber"/>
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ form.registrationNumber || "-" }}
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
