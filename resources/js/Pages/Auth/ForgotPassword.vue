<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout :title="lang().label.forgot_password">
        <Head :title="lang().label.forgot_password"/>
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo/>
            </template>
            <div class="flex flex-col mb-4">
                <h2 class="text-primary font-semibold text-xl">{{ lang().label.forgot_password }}</h2>
                <small class="text-slate-400">{{ lang().label.forgot_password_caption }}</small>
            </div>
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel :value="lang().label.email" for="email"/>
                    <TextInput id="email" v-model="form.email" :error="form.errors.email"
                               :placeholder="lang().placeholder.email" autocomplete="username" autofocus
                               class="mt-1 block w-full" required
                               type="email"/>
                    <InputError :message="form.errors.email" class="mt-2"/>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ lang().button.email_password_reset_link }} {{ form.processing ? '...' : '' }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
