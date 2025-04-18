<script setup>
import Table from "@/Components/Table.vue";
import {computed, ref} from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";
import PieChart from "./partials/PieChart.vue";
import LineChart from "./partials/LineChart.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    userRole: String,
    pieCharts: Object,
    statuses: Object,
    translations: Object,
    lineChartData: Array,
    perPage: Number,
});
const licenseTable = ref({
    visible: false,
    label: null,
    data: []
});
const certificateTable = ref({
    visible: false,
    label: null,
    data: []
});

const reverseStatusMap = Object.entries(props.statuses).reduce((acc, [status, label]) => {
    acc[label] = status;
    return acc;
}, {});

const handleCertificateClick = (label) => {
    licenseTable.value.visible = false;
    const status = reverseStatusMap[label] || 'valid';
    if (certificateTable.value.label === label && certificateTable.value.visible) {
        certificateTable.value.visible = false;
        return;
    }

    router.visit(route('dashboard'), {
        method: 'get',
        data: {
            data: 'certificate',
            status,
            field: 'validUntil',
            order: 'asc',
            perPage: 10
        },
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            certificateTable.value = {
                visible: true,
                label,
                data: page.props.certificates || []
            };
        }
    });
};

const handleLicenseClick = (label) => {
    const status = reverseStatusMap[label] || 'valid';

    certificateTable.value.visible = false;
    if (licenseTable.value.label === label && licenseTable.value.visible) {
        licenseTable.value.visible = false;
        return;
    }

    router.visit(route('dashboard'), {
        method: 'get',
        data: {
            data: 'licency',
            status,
            field: 'validUntil',
            order: 'asc',
            perPage: 10
        },
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            licenseTable.value = {
                visible: true,
                label,
                data: page.props.licenses || []
            };
        }
    });
};


const LayoutComponent = computed(() => {
    return props.userRole === "superuser" ? AppLayout : AppLayout;
});
</script>

<template>
    <component :is="LayoutComponent" title="Dashboard">
        <template #title>
            <span>Dashboard</span>
        </template>
        <div class="py-6">
            <div class="max-w-full mx-auto p-6">
                <h1 class="text-2xl font-bold mb-4">Selamat Datang</h1>

                <div v-if="pieCharts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <div class="bg-white dark:bg-slate-800 shadow-md rounded-md h-80 flex flex-col p-2">
                        <div class="flex justify-between items-center mb-4 px-3">
                            <h2 class="text-xl font-semibold capitalize text-start">{{ lang().label.employee }}</h2>
                        </div>

                        <div class="flex-grow flex items-center justify-center">
                            <PieChart :data="pieCharts.genderDistribution"
                                      class="w-auto h-auto max-w-[90%] max-h-[90%] aspect-square"/>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-md rounded-md h-80 flex flex-col p-2">
                        <div class="flex justify-between items-center mb-4 px-3">
                            <h2 class="text-xl font-semibold capitalize text-start">{{ lang().label.warning_str }}</h2>

                        </div>
                        <div class="flex-grow flex items-center justify-center">
                            <PieChart :data="pieCharts.certificateStatus" @segmentClicked="handleCertificateClick"
                                      class="w-auto h-auto max-w-[90%] max-h-[90%] aspect-square"/>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-md rounded-md h-80 flex flex-col p-2">
                        <div class="flex justify-between items-center mb-4 px-3">
                            <h2 class="text-xl font-semibold capitalize text-start">{{ lang().label.warning_sip }}</h2>

                        </div>
                        <div class="flex-grow flex items-center justify-center">
                            <PieChart :data="pieCharts.licenseStatus" @segmentClicked="handleLicenseClick"
                                      class="w-auto h-auto max-w-[90%] max-h-[90%] aspect-square"/>
                        </div>
                    </div>
                </div>
                <div v-if="lineChartData" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 mt-2">
                    <div class="col-span-12 bg-white dark:bg-slate-800 p-6 shadow-md rounded-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold capitalize text-start">
                                {{ lang().label.status_certificate_and_license }}</h2>
                        </div>
                        <LineChart :data="lineChartData"/>
                    </div>
                </div>
                <div v-if="certificateTable.visible"
                     class="mt-4 bg-white dark:bg-slate-800 p-4 rounded shadow col-span-12">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-semibold">Data STR - {{ certificateTable.label }}</h3>
                        <button @click="certificateTable.visible = false"
                                class="text-sm text-red-600 hover:underline">Tutup
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <Table>
                            <template #table-head>
                                <tr>
                                    <th class="p-4 text-center w-5">#</th>
                                    <th class="p-4 text-start">{{ lang().label.Employee }}</th>
                                    <th class="p-4 text-start">{{ lang().label.type }}</th>
                                    <th class="p-4 text-start">{{ lang().label.competence }}</th>
                                    <th class="p-4 text-start">{{ lang().label.RegistrationNumber }}</th>
                                    <th class="p-4 text-start">{{ lang().label.ValidFrom }}</th>
                                    <th class="p-4 text-start">{{ lang().label.ValidUntil }}</th>
                                </tr>
                            </template>

                            <template #table-body>
                                <tr v-for="(item, index) in certificateTable.data" :key="index"
                                    class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                    <td class="whitespace-nowrap px-2 py-1 text-center">{{ index + 1 }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.name }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.type }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.competence }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.registrationNumber }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.validFrom }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.validUntil }}</td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
                <div v-if="licenseTable.visible" class="mt-4 bg-white dark:bg-slate-800 p-4 rounded shadow col-span-12">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-semibold">Data SIP - {{ licenseTable.label }}</h3>
                        <button @click="licenseTable.visible = false"
                                class="text-sm text-red-600 hover:underline">Tutup
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <Table>
                            <template #table-head>
                                <tr>
                                    <th class="p-4 text-center w-5">#</th>
                                    <th class="p-4 text-start">{{ lang().label.Employee }}</th>
                                    <th class="p-4 text-start">{{ lang().label.type }}</th>
                                    <th class="p-4 text-start">{{ lang().label.RegistrationNumber }}</th>
                                    <th class="p-4 text-start">{{ lang().label.ValidFrom }}</th>
                                    <th class="p-4 text-start">{{ lang().label.ValidUntil }}</th>
                                </tr>
                            </template>

                            <template #table-body>
                                <tr v-for="(item, index) in licenseTable.data" :key="index"
                                    class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                    <td class="whitespace-nowrap px-2 py-1 text-center">{{ index + 1 }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.name }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.type }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.registrationNumber }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.validFrom }}</td>
                                    <td class="whitespace-nowrap px-2 py-1">{{ item.validUntil }}</td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>
