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
import SelectInput from "@/Components/SelectInput.vue";
import {EyeIcon, EyeSlashIcon, PencilIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);
const props = defineProps({
    title: String,
    user: Object,
});

const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    phone_number: "",
    password: "",
    password_confirmation: "",
});

onUpdated(() => {
    if (show) {
        form.first_name = props.user?.first_name;
        form.last_name = props.user?.last_name;
        form.email = props.user?.email;
        form.phone_number = props.user?.phone_number;
    }
});

const submit = () => {
    form.put(route("admin.update", props.user?.id), {
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

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

</script>
<template>
    <div>
        <ActionButton v-tooltip="lang().label.edit" @click.prevent="(show = true), emit('open')">
            <PencilIcon class="w-4 h-auto"/>
        </ActionButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.edit }} {{ props.title }}
            </template>

            <template #content>
                <form class="space-y-2" @submit.prevent="submit">
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <InputLabel :value="lang().label.first_name" for="first_name"/>
                            <TextInput id="first_name" v-model="form.first_name" :error="form.errors.first_name"
                                       :placeholder="lang().placeholder.first_name"
                                       autocomplete="first_name"
                                       autofocus
                                       class="mt-1 block w-full" required
                                       type="text"/>
                            <InputError :message="form.errors.first_name" class="mt-2"/>
                        </div>

                        <div class="w-1/2">
                            <InputLabel :value="lang().label.last_name" for="last_name"/>
                            <TextInput id="last_name" v-model="form.last_name" :error="form.errors.last_name"
                                       :placeholder="lang().placeholder.last_name"
                                       autocomplete="last_name"
                                       autofocus
                                       class="mt-1 block w-full" required
                                       type="text"/>
                            <InputError :message="form.errors.last_name" class="mt-2"/>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <InputLabel :value="lang().label.email" for="email"/>
                        <TextInput
                                id="email"
                                v-model="form.email"
                                :error="form.errors.email"
                                :placeholder="lang().placeholder.email"
                                class="block w-full"
                                type="email"
                                @keyup.enter="submit"
                        />
                        <InputError :message="form.errors.email"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel :value="lang().label.phone_number" for="phone_number"/>
                        <TextInput
                                id="phone_number"
                                v-model="form.phone_number"
                                :error="form.errors.phone_number"
                                :placeholder="lang().placeholder.phone_number"
                                class="block w-full"
                                type="text"
                                @keyup.enter="submit"
                        />
                        <InputError :message="form.errors.email"/>
                    </div>

                    <div class="space-y-1 relative">
                        <InputLabel :value="lang().label.password" for="password"/>
                        <div class="relative">
                            <TextInput
                                    id="password"
                                    v-model="form.password"
                                    :error="form.errors.password"
                                    :placeholder="lang().placeholder.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block w-full pr-10"
                                    @keyup.enter="submit"
                            />
                            <button class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500"
                                    type="button"
                                    @click="showPassword = !showPassword">
                                <EyeSlashIcon v-if="showPassword" class="h-5 w-5"/>
                                <EyeIcon v-else class="h-5 w-5"/>
                            </button>
                        </div>
                        <InputError :message="form.errors.password"/>
                    </div>
                    <div class="space-y-1 relative">
                        <InputLabel :value="lang().label.password_confirmation" for="password_confirmation"/>
                        <div class="relative">
                            <TextInput
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :error="form.errors.password_confirmation"
                                    :placeholder="lang().placeholder.password"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    class="block w-full pr-10"
                                    @keyup.enter="submit"
                            />
                            <button class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500"
                                    type="button"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation">
                                <EyeSlashIcon v-if="showPasswordConfirmation" class="h-5 w-5"/>
                                <EyeIcon v-else class="h-5 w-5"/>
                            </button>
                        </div>
                        <InputError :message="form.errors.password_confirmation"/>
                    </div>
                </form>
            </template>

            <template #footer>
                <div class="flex w-fit rounded overflow-hidden">
                    <SecondaryButton @click="closeModal">
                        {{ lang().button.cancel }}
                    </SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="submit">
                        {{ lang().button.save }} {{ form.processing ? "..." : "" }}
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </div>
</template>
