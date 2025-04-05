<script setup>
import { computed, resolveComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";
import PieChart from "./partials/PieChart.vue";
import LineChart from "./partials/LineChart.vue";
import { ArrowRightIcon } from "@heroicons/vue/24/outline";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    userRole: String,
    pieCharts: Object,
    lineChartData: Array
});

const LayoutComponent = computed(() => {
    return props.userRole === "superuser" ? AppLayout : GuestLayout;
});
</script>

<template>
    <component :is="LayoutComponent" title="Dashboard">
        <template #title>
            <span>Dashboard</span>
        </template>

        <template v-if="userRole === 'superuser'" #breadcrumb>
            <Breadcrumb />
        </template>

        <div class="py-6">
            <div class="max-w-full mx-auto p-6">
                <h1 class="text-2xl font-bold mb-4">Selamat Datang</h1>

                <div v-if="pieCharts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <div class="bg-white dark:bg-slate-800 shadow-md rounded-md h-80 flex flex-col p-2">
                        <div class="flex justify-between items-center mb-4 px-3">
                            <h2 class="text-xl font-semibold capitalize text-start">{{ lang().label.employee }}</h2>
                            <Link :href="route('dashboard.employee')" class="flex items-center px-3 py-1 text-sm font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <ArrowRightIcon class="w-4 h-4" />
                            </Link>
                        </div>

                        <div class="flex-grow flex items-center justify-center">
                            <PieChart :data="pieCharts.genderDistribution" class="w-auto h-auto max-w-[90%] max-h-[90%] aspect-square" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-md rounded-md h-80 flex flex-col p-2">
                        <div class="flex justify-between items-center mb-4 px-3">
                            <h2 class="text-xl font-semibold capitalize text-start">{{ lang().label.registrationcertificate }}</h2>
                            <Link :href="route('dashboard.certificates')" class="flex items-center px-3 py-1 text-sm font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <ArrowRightIcon class="w-4 h-4" />
                            </Link>
                        </div>
                        <div class="flex-grow flex items-center justify-center">
                            <PieChart :data="pieCharts.certificateValidity" class="w-auto h-auto max-w-[90%] max-h-[90%] aspect-square" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-md rounded-md h-80 flex flex-col p-2">
                        <div class="flex justify-between items-center mb-4 px-3">
                            <h2 class="text-xl font-semibold capitalize text-start">{{ lang().label.license }}</h2>
                            <Link :href="route('dashboard.licenses')" class="flex items-center px-3 py-1 text-sm font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <ArrowRightIcon class="w-4 h-4" />
                            </Link>
                        </div>
                        <div class="flex-grow flex items-center justify-center">
                            <PieChart :data="pieCharts.licenseValidity" class="w-auto h-auto max-w-[90%] max-h-[90%] aspect-square" />
                        </div>
                    </div>
                </div>

                <div v-if="lineChartData" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 mt-2">
                    <div class="col-span-12 bg-white dark:bg-slate-800 p-6 shadow-md rounded-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold capitalize text-start">Total {{ lang().label.employee }}</h2>
                        </div>

                        <LineChart :data="lineChartData" />
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>
