<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {ref} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <GuestLayout :title="lang().label.secure_area">
        <Head :title="lang().label.secure_area"/>
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo/>
            </template>
            <div class="flex flex-col mb-4">
                <h2 class="text-primary font-semibold text-xl">{{ lang().label.confirm_password }}</h2>
                <small class="text-slate-400">{{ lang().label.confirm_password_caption }}</small>
            </div>
            <form @submit.prevent="submit">
                <div>
                    <InputLabel :value="lang().label.password" for="password"/>
                    <TextInput id="password" ref="passwordInput" v-model="form.password" :error="form.errors.password"
                               :placeholder="lang().placeholder.password" autocomplete="current-password" autofocus
                               class="mt-1 block w-full" required type="password"/>
                    <InputError :message="form.errors.password" class="mt-2"/>
                </div>

                <div class="flex justify-end mt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-4">
                        {{ lang().button.confirm }} {{ form.processing ? '...' : '' }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
