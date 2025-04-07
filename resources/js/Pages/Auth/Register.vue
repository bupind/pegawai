<script setup>
import {ref} from 'vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {EyeIcon, EyeSlashIcon} from '@heroicons/vue/24/outline';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DropdownLoader from "@/Components/DropdownLoader.vue";

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    bumn_id: '',
    terms: false,
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :title="lang().label.register">
        <Head :title="lang().label.register"/>
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo/>
            </template>

            <div class="flex flex-col mb-4">
                <h2 class="text-primary font-semibold text-xl">{{ lang().label.register }}</h2>
                <small class="text-slate-400">{{ lang().label.register_caption }}</small>
            </div>

            <form @submit.prevent="submit">
                <div class="flex gap-4 mt-4">
                    <div class="w-1/2">
                        <InputLabel :value="lang().label.first_name" for="first_name"/>
                        <TextInput
                                id="first_name"
                                v-model="form.first_name"
                                :error="form.errors.first_name"
                                :placeholder="lang().placeholder.first_name"
                                autocomplete="first_name"
                                required
                                class="mt-1 block w-full"
                                type="text"
                        />
                        <InputError :message="form.errors.first_name" class="mt-2"/>
                    </div>

                    <div class="w-1/2">
                        <InputLabel :value="lang().label.last_name" for="last_name"/>
                        <TextInput
                                id="last_name"
                                v-model="form.last_name"
                                :error="form.errors.last_name"
                                :placeholder="lang().placeholder.last_name"
                                autocomplete="last_name"
                                required
                                class="mt-1 block w-full"
                                type="text"
                        />
                        <InputError :message="form.errors.last_name" class="mt-2"/>
                    </div>
                </div>

                <div class="mt-4">
                    <InputLabel :value="lang().label.phone_number" for="phone_number"/>
                    <TextInput
                            id="phone_number"
                            v-model="form.phone_number"
                            :error="form.errors.phone_number"
                            :placeholder="lang().placeholder.phone_number"
                            autocomplete="phone_number"
                            required
                            class="mt-1 block w-full"
                            type="text"
                    />
                    <InputError :message="form.errors.phone_number" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel :value="lang().label.email" for="email"/>
                    <TextInput
                            id="email"
                            v-model="form.email"
                            :error="form.errors.email"
                            :placeholder="lang().placeholder.email"
                            autocomplete="username"
                            required
                            class="mt-1 block w-full"
                            type="email"
                    />
                    <InputError :message="form.errors.email" class="mt-2"/>
                </div>

                <div class="mt-4 relative">
                    <InputLabel :value="lang().label.password" for="password"/>
                    <TextInput
                            id="password"
                            v-model="form.password"
                            :error="form.errors.password"
                            :placeholder="lang().placeholder.password"
                            :type="showPassword ? 'text' : 'password'"
                            autocomplete="new-password"
                            required
                            class="mt-1 block w-full pr-10"
                    />
                    <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-9 text-gray-500">
                        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5"/>
                    </button>
                    <InputError :message="form.errors.password" class="mt-2"/>
                </div>
                <div class="mt-4 relative">
                    <InputLabel :value="lang().label.confirm_password" for="password_confirmation"/>
                    <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            :error="form.errors.password_confirmation"
                            :placeholder="lang().placeholder.password"
                            :type="showPasswordConfirm ? 'text' : 'password'"
                            autocomplete="new-password"
                            required
                            class="mt-1 block w-full pr-10"
                    />
                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                            class="absolute right-3 top-9 text-gray-500">
                        <component :is="showPasswordConfirm ? EyeSlashIcon : EyeIcon" class="w-5 h-5"/>
                    </button>
                    <InputError :message="form.errors.password_confirmation" class="mt-2"/>
                </div>

                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                    <Checkbox id="terms" v-model:checked="form.terms" required/>
                    <label for="terms" class="ml-2 text-sm">
                        {{ lang().label.i_agree_to_the }}
                        <a :href="route('terms.show')" class="underline text-sm text-slate-600 hover:text-slate-900"
                           target="_blank">
                            {{ lang().label.terms_of_service }}
                        </a>
                        {{ lang().label.and }}
                        <a :href="route('policy.show')" class="underline text-sm text-slate-600 hover:text-slate-900"
                           target="_blank">
                            {{ lang().label.privacy_policy }}
                        </a>
                    </label>
                    <InputError :message="form.errors.terms" class="mt-2"/>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <Link :href="route('login')" class="underline text-sm text-slate-600 hover:text-slate-900">
                        {{ lang().label.already_registered }}
                    </Link>

                    <PrimaryButton :disabled="form.processing" class="ml-4">
                        {{ lang().button.register }} {{ form.processing ? '...' : '' }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
