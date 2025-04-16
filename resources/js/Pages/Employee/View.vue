<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import ActionButton from "@/Components/ActionButton.vue";
import {useForm} from "@inertiajs/vue3";
import {onUpdated, ref} from "vue";
import {EyeIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);

const props = defineProps({
    title: String,
    canLogins: String,
    employee: Object,
    statuses: Object,
    genders: Object,
    types: Object,
});

const form = useForm({
    code: "",
    name: "",
    gender: "",
    type: "",
    status: "",
    ...(props.canLogins === 'true' && {
        email: "",
        phone_number: "",
    }),
});

onUpdated(() => {
    if (show.value && props.employee) {
        form.code = props.employee.code ?? "";
        form.name = props.employee.name ?? "";
        form.gender = props.employee.gender ?? "";
        form.type = props.employee.type ?? "";
        form.status = props.employee.status ?? "";
        if (props.canLogins === true || props.canLogins === 'true') {
            form.email = props.employee?.user?.email || "";
            form.phone_number = props.employee?.user?.phone_number || "";
        }
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
                            <th class="px-4 py-2 w-1/3 font-medium">{{ lang().label.code }}</th>
                            <td class="px-4 py-2">{{ form.code || '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.employee }}</th>
                            <td class="px-4 py-2">{{ form.name || '-' }}</td>
                        </tr>

                        <tr class="border-b" v-if="props.canLogins === 'true'">
                            <th class="px-4 py-2 font-medium">{{ lang().label.email }}</th>
                            <td class="px-4 py-2">{{ form.email || '-' }}</td>
                        </tr>

                        <tr class="border-b" v-if="props.canLogins === 'true'">
                            <th class="px-4 py-2 font-medium">{{ lang().label.phone_number }}</th>
                            <td class="px-4 py-2">{{ form.phone_number || '-' }}</td>
                        </tr>

                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.Gender }}</th>
                            <td class="px-4 py-2">{{ props.genders[form.gender] || '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="px-4 py-2 font-medium">{{ lang().label.type }}</th>
                            <td class="px-4 py-2">{{ props.types[form.type] || '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 font-medium">{{ lang().label.status }}</th>
                            <td class="px-4 py-2">{{ props.statuses[form.status] || '-' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </template>

        </DialogModal>
    </div>
</template>
