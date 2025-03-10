<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {onUpdated, ref} from "vue";
import {PencilIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);
const props = defineProps({
    title: String,
    roles: Object,
    permission: Object,
});

const form = useForm({
    name: "",
    guard_name: "web",
});

onUpdated(() => {
    if (show) {
        form.name = props.permission?.name;
    }
});

const submit = () => {
    form.put(route("permission.update", props.permission?.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => null,
        onFinish: () => null,
    });
};

const closeModal = () => {
    show.value = false;
    form.errors = {};
    form.reset();
};

const roles = props.roles?.map((role) => ({
    label: role.name,
    value: role.name,
}));
</script>
<template>
    <div>
        <ActionButton
                v-tooltip="lang().label.edit"
                @click.prevent="(show = true), emit('open')"
        >
            <PencilIcon class="w-4 h-auto"/>
        </ActionButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.edit }} {{ props.title }}
            </template>

            <template #content>
                <form class="space-y-2" @submit.prevent="submit">
                    <div class="space-y-1">
                        <InputLabel :value="lang().label.name" for="name"/>
                        <TextInput
                                id="name"
                                v-model="form.name"
                                :error="form.errors.name"
                                :placeholder="lang().placeholder.permission_name"
                                autocomplete="name"
                                class="block w-full"
                                type="text"
                        />
                        <InputError :message="form.errors.name"/>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    {{ lang().button.cancel }}
                </SecondaryButton>

                <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="ml-3"
                        @click="submit"
                >
                    {{ lang().button.save }} {{ form.processing ? "..." : "" }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
