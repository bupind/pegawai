<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="lang().label.current_password" for="current_password"/>
                <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                           :error="form.errors.current_password"
                           :placeholder="lang().placeholder.password" autocomplete="current-password"
                           class="mt-1 block w-full"
                           type="password"/>
                <InputError :message="form.errors.current_password" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="lang().label.new_password" for="password"/>
                <TextInput id="password" ref="passwordInput" v-model="form.password" :error="form.errors.password"
                           :placeholder="lang().placeholder.password" autocomplete="new-password"
                           class="mt-1 block w-full"
                           type="password"/>
                <InputError :message="form.errors.password" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="lang().label.new_password" for="password_confirmation"/>
                <TextInput id="password_confirmation" v-model="form.password_confirmation"
                           :error="form.errors.password_confirmation"
                           :placeholder="lang().placeholder.password" autocomplete="new-password"
                           class="mt-1 block w-full"
                           type="password"/>
                <InputError :message="form.errors.password_confirmation" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                {{ lang().label.saved }}
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ lang().button.save }} {{ form.processing ? '...' : '' }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>
