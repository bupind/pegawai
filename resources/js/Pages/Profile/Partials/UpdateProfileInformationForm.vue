<script setup>
import {ref} from 'vue';
import {Link, router, useForm} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    phone_number: props.user.phone_number,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            {{ lang().label.profile_information }}
        </template>

        <template #description>
            {{ lang().label.profile_information_description }}
        </template>

        <template #form>
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <input ref="photoInput" class="hidden" type="file" @change="updatePhotoPreview">
                <InputLabel :value="lang().label.photo" for="photo"/>
                <div v-show="!photoPreview" class="mt-2">
                    <img :alt="user.first_name" :src="user.full_photo" class="rounded h-20 w-20 object-cover">
                </div>
                <div v-show="photoPreview" class="mt-2">
                    <span :style="'background-image: url(\'' + photoPreview + '\');'"
                          class="block rounded w-20 h-20 bg-cover bg-no-repeat bg-center"/>
                </div>
                <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    {{ lang().button.select_new_photo }}
                </SecondaryButton>
                <SecondaryButton v-if="user.profile_photo_path" class="mt-2" type="button" @click.prevent="deletePhoto">
                    {{ lang().button.remove_photo }}
                </SecondaryButton>
                <InputError :message="form.errors.photo" class="mt-2"/>
            </div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <InputLabel :value="lang().label.first_name" for="first_name"/>
                    <TextInput id="first_name" v-model="form.first_name" :error="form.errors.first_name"
                               :placeholder="lang().placeholder.first_name" autocomplete="first_name"
                               autofocus
                               class="mt-1 block w-full" required
                               type="text"/>
                    <InputError :message="form.errors.first_name" class="mt-2"/>
                </div>

                <div class="w-1/2">
                    <InputLabel :value="lang().label.last_name" for="last_name"/>
                    <TextInput id="last_name" v-model="form.last_name" :error="form.errors.last_name"
                               :placeholder="lang().placeholder.last_name" autocomplete="last_name"
                               autofocus
                               class="mt-1 block w-full" required
                               type="text"/>
                    <InputError :message="form.errors.last_name" class="mt-2"/>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="lang().label.phone_number" for="phone_number"/>
                <TextInput id="phone_number" v-model="form.phone_number" :error="form.errors.phone_number"
                           :placeholder="lang().placeholder.phone_number"
                           autocomplete="phone_number" class="mt-1 block w-full"
                           type="text"/>
                <InputError :message="form.errors.phone_number" class="mt-2"/>
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel :value="lang().label.email" for="email"/>
                <TextInput id="email" v-model="form.email" :error="form.errors.email"
                           :placeholder="lang().placeholder.email"
                           autocomplete="username"
                           class="mt-1 block w-full" type="email"/>
                <InputError :message="form.errors.email" class="mt-2"/>

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2 dark:text-white">
                        {{ lang().label.email_address_is_unverified }}

                        <Link :href="route('verification.send')" as="button"
                              class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-slate-800"
                              method="post"
                              @click.prevent="sendEmailVerification">
                            {{ lang().label.click_here_to_resend_verification_email }}
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent"
                         class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ lang().label.an_new_verification_link }}
                    </div>
                </div>
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
