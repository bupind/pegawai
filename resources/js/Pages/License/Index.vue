<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Table from "@/Components/Table.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TablePagination from "@/Components/TablePagination.vue";
import TextInput from "@/Components/TextInput.vue";
import Create from "@/Pages/License/Create.vue";
import Edit from "@/Pages/License/Edit.vue";
import Delete from "@/Pages/License/Delete.vue";
import DeleteBulk from "@/Pages/License/DeleteBulk.vue";
import {reactive, watch} from "vue";
import pkg from "lodash";
import {router} from "@inertiajs/vue3";
import {ChevronUpDownIcon} from "@heroicons/vue/24/outline";
import Checkbox from "@/Components/Checkbox.vue";

const {_, debounce, pickBy} = pkg;
const props = defineProps({
    title: String,
    filters: Object,
    datas: Object,
    breadcrumbs: Object,
    statuses: Object,
    types: Object,
    perPage: Number,
});

const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    val: null,
});

const order = (field) => {
    data.params.field = field;
    data.params.order = data.params.order === "asc" ? "desc" : "asc";
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("license.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150)
);

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = [];
    } else {
        props.datas?.data.forEach((val) => {
            data.selectedId.push(val.id);
        });
    }
};
const select = () => {
    if (props.datas?.data.length == data.selectedId.length) {
        data.multipleSelect = true;
    } else {
        data.multipleSelect = false;
    }
};
const headers = [
    {label: 'Employee', ordered: 'employeeId'},
    {label: 'RegistrationCertificate', ordered: 'registrationCertificateId'},
    {label: 'Type', ordered: 'type'},
    {label: 'RegistrationNumber', ordered: 'registrationNumber'},
    {label: 'RecommendationNumber', ordered: 'recommendationNumber'},
    {label: 'ValidFrom', ordered: 'validFrom'},
    {label: 'ValidUntil', ordered: 'validUntil'},
    {label: 'status', ordered: 'status'},
    // {label: 'created', ordered: 'created_at'},
];

const bodys = [
    {label: 'Employee', value: (val) => val?.employee?.name || '-'},
    {label: 'RegistrationCertificate', value: (val) => val?.certificate?.registrationNumber || '-'},
    {label: 'Type', value: (val) => props.types[val.type] || '-'},
    {label: 'RegistrationNumber', value: (val) => val.registrationNumber || '-'},
    {label: 'RecommendationNumber', value: (val) => val.recommendationNumber || '-'},
    {label: 'ValidFrom', value: (val) => val.validFrom || '-'},
    {label: 'ValidUntil', value: (val) => val.validUntil || '-'},
    {label: 'status', value: (val) => props.statuses[val.status] || '-'},
];


</script>

<template>
    <AppLayout :title="props.title">
        <template #title>
            <span>{{ props.title }}</span>
        </template>
        <template #breadcrumb>
            <Breadcrumb/>
        </template>
        <div class="py-6">
            <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-4">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow sm:rounded">
                    <Table>
                        <template #table-action>
                            <div class="flex shrink-0 rounded overflow-hidden">
                                <SelectInput v-model="data.params.perPage" :dataSet="$page.props.app.perpage"
                                             class="h-9 text-sm px-3"/>
                                <Create v-show="can(['license create'])" :title="props.title"
                                        :statuses="props.statuses" :types="props.types"/>
                                <DeleteBulk v-show=" data.selectedId.length != 0 && can(['license delete'])
                                    "
                                            :selectedId="data.selectedId"
                                            :title="props.title"
                                            @close="
                                        (data.selectedId = []),
                                            (data.multipleSelect = false)
                                    "
                                />
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-0">
                                    <button class="flex items-center justify-center w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-l-md hover:bg-gray-300 dark:hover:bg-gray-600"
                                            @click="toggleSearch">
                                        <MagnifyingGlassIcon class="w-5 h-5 text-gray-950 dark:text-gray-300"/>
                                    </button>
                                    <TextInput v-if="showSearch" v-model="data.params.search"
                                               :placeholder="lang().placeholder.search"
                                               class="block h-9 transition-all duration-300"
                                               type="text"/>
                                    <button class="flex items-center px-3 py-2 bg-green-500 text-white border border-green-600 rounded-r-md hover:bg-green-600"
                                            @click="exportData('all')">
                                        <ArrowDownTrayIcon class="w-5 h-5 mr-2"/>
                                        <span class="text-sm">All</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template #table-head>
                            <tr>
                                <th class="p-4 text-center w-5">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll"/>
                                </th>
                                <th class="p-4 text-center w-5">#</th>
                                <th v-for="col in headers" :key="col.label" class="p-4 cursor-pointer"
                                    @click="col.ordered ? order(col.ordered) : null">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label[col.label] }}</span>
                                        <ChevronUpDownIcon v-if="col.ordered" class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </template>
                        <template #table-body>
                            <tr v-for="(val, index) in datas.data" :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap px-2 py-1">
                                    <input class="rounded dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-slate-800 dark:checked:bg-primary dark:checked:border-primary"
                                           type="checkbox" @change="select" :value="val.id"
                                           v-model="data.selectedId"/>
                                </td>
                                <td class="whitespace-nowrap px-2 py-1 text-center">{{ index + 1 }}</td>
                                <td v-for="col in bodys" :key="col.label" class="whitespace-nowrap px-2 py-1">
                                    {{ col.value(val) }}
                                </td>
                                <td class="whitespace-nowrap flex justify-end px-2 py-1">
                                    <div class="flex w-fit rounded overflow-hidden">
                                        <Edit v-show="can(['license update'])" :title="props.title"
                                              :statuses="props.statuses" :license="data.val" :types="props.types"
                                              @open="data.val = val"/>
                                        <Delete v-show="can(['license delete'])" :title="props.title"
                                                :val="data.val" @open="data.val = val"/>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template #table-pagination>
                            <TablePagination :links="props.datas" :filters="data.params"/>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
