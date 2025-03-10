<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";

const props = defineProps({
    userRole: String,
    totalUsers: Number,
    totalSubmissions: Number,
    totalCompanies: Number,
    totalUsersToday: Number,
    totalSubmissionsToday: Number,
    totalCompaniesToday: Number,
    chart: Object
});


const LayoutComponent = props.userRole === "superuser" ? AppLayout : GuestLayout;
</script>

<template>
    <component :is="LayoutComponent" title="Dashboard">
        <template #title>
            <span>Dashboard</span>
        </template>

        <template v-if="userRole === 'superuser'" #breadcrumb>
            <Breadcrumb/>
        </template>

        <div class="py-6">
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                <div v-if="userRole === 'superuser'">
                    <h1 class="text-2xl font-bold mb-4">Selamat Datang</h1>
                </div>

                <div v-else>
                    <div class="container mx-auto max-w-fit mt-6">
                        <h2 class="text-xl font-semibold mb-4">Selamat Datang</h2>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>
