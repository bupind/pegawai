<script setup>
import {ref} from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {EyeIcon, EyeSlashIcon} from '@heroicons/vue/24/outline';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const showPassword = ref(false);

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout :title="lang().label.login">
        <Head :title="lang().label.login"/>
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo/>
            </template>
            <div class="flex flex-col mb-4">
                <h2 class="text-primary font-semibold text-xl">{{ lang().label.login }}</h2>
                <small class="text-slate-400">{{ lang().label.login_caption }}</small>
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

                <div class="mt-4 relative">
                    <InputLabel :value="lang().label.password" for="password"/>
                    <TextInput id="password" v-model="form.password" :error="form.errors.password"
                               :placeholder="lang().placeholder.password" :type="showPassword ? 'text' : 'password'"
                               autocomplete="current-password"
                               class="mt-1 block w-full pr-10" required/>
                    <button class="absolute right-3 top-9 text-gray-500 dark:text-gray-400" type="button"
                            @click="showPassword = !showPassword">
                        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5"/>
                    </button>
                    <InputError :message="form.errors.password" class="mt-2"/>
                </div>

                <div class="block mt-4">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.remember" name="remember"/>
                        <span class="ml-2 text-sm text-slate-600 dark:text-slate-400">{{
                            lang().label.remember_me
                            }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <Link v-if="canResetPassword" :href="route('password.request')"
                          class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-slate-800">
                        {{ lang().label.forgot_your_password }}
                    </Link>

                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-4">
                        {{ lang().button.login }} {{ form.processing ? '...' : '' }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
