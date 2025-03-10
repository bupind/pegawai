<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {PlusIcon} from "@heroicons/vue/24/outline";

const show = ref(false);
const props = defineProps({
    title: String,
});

const form = useForm({
    name: "",
    guard_name: "web",
});

const submit = () => {
    form.post(route("permission.store"), {
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
</script>
<template>
    <div>
        <PrimaryButton
                class="flex rounded-none items-center justify-start gap-2"
                @click.prevent="show = true"
        >
            <PlusIcon class="w-4 h-auto"/>
            <span class="hidden md:block">{{ lang().button.add }}</span>
        </PrimaryButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.add }} {{ props.title }}
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
