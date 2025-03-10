<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import TabPanels from '@/components/TabPanels.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const activeTab = ref('profile');

const tabs = [
    { label: 'Profile Info', value: 'profile' },
    { label: 'Password', value: 'password' },
    { label: 'Security', value: 'security' }
];
</script>

<template>
    <GuestLayout title="Profile">
        <template #title>
            <span>Profile</span>
        </template>

        <div class="max-w-7xl px-4 mx-auto sm:px-6 lg:px-8 mt-10">
            <TabPanels v-model:activeTab="activeTab" :tabs="tabs">
                <template #profile>
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </template>

                <template #password>
                    <UpdatePasswordForm class="mt-6" />
                </template>

                <template #security>
                    <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" class="mt-6" />
                    <SectionBorder class="mt-6" />
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-6" />
                </template>
            </TabPanels>
        </div>
    </GuestLayout>
</template>
