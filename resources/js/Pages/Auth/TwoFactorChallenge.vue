<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {nextTick, ref} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <GuestLayout :title="lang().label.two_factor_confirmation">
        <Head :title="lang().label.two_factor_confirmation"/>
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo/>
            </template>
            <div class="flex flex-col mb-4">
                <h2 class="text-primary font-semibold text-xl">{{ lang().label.two_factor_confirmation }}</h2>
                <small class="text-slate-400">{{ lang().label.two_factor_confirmation_caption }}</small>
            </div>

            <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
                <template v-if="!recovery">
                    {{ lang().label.please_confirm_access }}
                </template>

                <template v-else>
                    {{ lang().label.please_confirm_access_recovery }}
                </template>
            </div>

            <form @submit.prevent="submit">
                <div v-if="!recovery">
                    <InputLabel :value="lang().label.code" for="code"/>
                    <TextInput id="code" ref="codeInput" v-model="form.code" :error="form.errors.code"
                               :placeholder="lang().placeholder.code"
                               autocomplete="one-time-code" autofocus class="mt-1 block w-full"
                               inputmode="numeric"
                               type="text"/>
                    <InputError :message="form.errors.code" class="mt-2"/>
                </div>

                <div v-else>
                    <InputLabel :value="lang().label.recovery_code" for="recovery_code"/>
                    <TextInput id="recovery_code" ref="recoveryCodeInput" v-model="form.recovery_code"
                               :error="form.errors.recovery_code"
                               :placeholder="lang().placeholder.recovery_code" autocomplete="one-time-code"
                               class="mt-1 block w-full"
                               type="text"/>
                    <InputError :message="form.errors.recovery_code" class="mt-2"/>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <button class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 underline cursor-pointer"
                            type="button"
                            @click.prevent="toggleRecovery">
                        <template v-if="!recovery">
                            {{ lang().label.use_recovery_code }}
                        </template>

                        <template v-else>
                            {{ lang().label.use_authentication_code }}
                        </template>
                    </button>

                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-4">
                        {{ lang().button.login }} {{ form.processing ? '...' : '' }}
                    </PrimaryButton>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
