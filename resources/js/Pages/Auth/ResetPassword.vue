<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :title="lang().label.reset_password">
        <Head :title="lang().label.reset_password"/>
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo/>
            </template>
            <div class="flex flex-col mb-4">
                <h2 class="text-primary font-semibold text-xl">{{ lang().label.reset_password }}</h2>
                <small class="text-slate-400">{{ lang().label.reset_password_caption }}</small>
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

                <div class="mt-4">
                    <InputLabel :value="lang().label.password" for="password"/>
                    <TextInput id="password" v-model="form.password" :error="form.errors.password"
                               :placeholder="lang().placeholder.password" autocomplete="new-password"
                               class="mt-1 block w-full" required
                               type="password"/>
                    <InputError :message="form.errors.password" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel :value="lang().label.confirm_password" for="password_confirmation"/>
                    <TextInput id="password_confirmation" v-model="form.password_confirmation"
                               :error="form.errors.password_confirmation"
                               :placeholder="lang().placeholder.password" autocomplete="new-password"
                               class="mt-1 block w-full"
                               required
                               type="password"/>
                    <InputError :message="form.errors.password_confirmation" class="mt-2"/>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ lang().button.reset_password }} {{ form.processing ? '...' : '' }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
