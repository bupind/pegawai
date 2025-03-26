<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";
import {ArrowLeftIcon} from "@heroicons/vue/24/outline";
import PieChart from "./partials/PieChart.vue";
import LineChart from "./partials/LineChart.vue";
import Certificate from "./partials/Certificate.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    userRole: String,
    title: String,
    pieCharts: Object,
    lineChartData: Array,
    filters: Object,
    certificateTable: Object,
    types: Object,
    statuses: Object,
    perPage: Number,
});

const LayoutComponent = props.userRole === "superuser" ? AppLayout : GuestLayout;

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.push("/dashboard");
    }
};
</script>

<template>
    <component :is="LayoutComponent" :title="title">
        <template #title>
            <span>{{ title }}</span>
        </template>

        <template v-if="userRole === 'superuser'" #breadcrumb>
            <Breadcrumb/>
        </template>

        <div class="py-6">
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                <div v-if="userRole === 'superuser'">
                    <div class="mb-4 flex items-center">
                        <button @click="goBack"
                                class="flex items-center px-4 py-2 text-sm font-medium bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            <ArrowLeftIcon class="w-5 h-5 mr-2"/>
                            {{ lang().button.back }}
                        </button>
                        <h1 class="text-2xl font-bold ml-2">{{ title }}</h1>
                    </div>

                    <div class="grid gap-2 md:grid-cols-12">
                        <div class="md:col-span-9 col-span-12 bg-white dark:bg-slate-800 p-4 shadow-md rounded-md">
                            <h2 class="text-xl font-semibold capitalize text-start mb-4">
                                Grafik Pendaftaran Sertifikat
                            </h2>
                            <LineChart :data="lineChartData" class="w-full h-full"/>
                        </div>
                        <div class="md:col-span-3 col-span-12 grid sm:grid-cols-2 md:grid-cols-1 gap-2">
                            <div class="bg-white dark:bg-slate-800 shadow-md rounded-md p-4 flex flex-col items-center">
                                <h2 class="text-lg font-semibold text-center pb-2">Status Sertifikat</h2>
                                <PieChart :data="pieCharts.certificateValidity"
                                          class="w-auto h-auto max-w-[85%] max-h-[85%] mx-auto aspect-square"/>
                            </div>
                            <div class="bg-white dark:bg-slate-800 shadow-md rounded-md p-4 flex flex-col items-center">
                                <h2 class="text-lg font-semibold text-center pb-2">Perbandingan Kepemilikan
                                    Sertifikat</h2>
                                <PieChart :data="pieCharts.certificateOwnership"
                                          class="w-auto h-auto max-w-[85%] max-h-[85%] mx-auto aspect-square"/>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 bg-white dark:bg-slate-800 p-4 shadow-md rounded-md">
                        <h2 class="text-xl font-semibold mb-4">Daftar Sertifikat</h2>
                        <Certificate
                                :certificateTable="certificateTable"
                                :types="types"
                                :statuses="statuses"
                                :filters="filters"
                                :perPage="perPage"/>
                    </div>
                </div>

                <div v-else-if="userRole === 'participant'">
                    <div class="container mx-auto max-w-fit mt-6">
                        <h2 class="text-xl font-semibold mb-4">Selamat Datang, Pengguna!</h2>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>
