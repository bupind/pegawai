<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Table from "@/Components/Table.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TablePagination from "@/Components/TablePagination.vue";
import TextInput from "@/Components/TextInput.vue";
import Create from "@/Pages/Pegawai/Create.vue";
import Edit from "@/Pages/Pegawai/Edit.vue";
import Delete from "@/Pages/Pegawai/Delete.vue";
import DeleteBulk from "@/Pages/Pegawai/DeleteBulk.vue";
import { reactive, watch } from "vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
import { ChevronUpDownIcon, MagnifyingGlassIcon} from "@heroicons/vue/24/outline";
import Checkbox from "@/Components/Checkbox.vue";

const { _, debounce, pickBy } = pkg;
const props = defineProps({
    title: String,
    filters: Object,
    pegawais: Object,
    pegawaipegawai: Object,
    statuses: Object,
    breadcrumbs: Object,
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
    pegawai: null,
});
const order = (field) => {
    data.params.field = field;
    data.params.order = data.params.order === "asc" ? "desc" : "asc";
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("pegawai.index"), params, {
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
        props.pegawais?.data.forEach((pegawai) => {
            data.selectedId.push(pegawai.id);
        });
    }
};
const select = () => {
    if (props.pegawais?.data.length == data.selectedId.length) {
        data.multipleSelect = true;
    } else {
        data.multipleSelect = false;
    }
};
</script>

<template>
    <AppLayout :title="props.title">
        <template #title>
            <span>{{ props.title }}</span>
        </template>
        <template #breadcrumb>
            <Breadcrumb />
        </template>

        <div class="py-6">
            <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-4">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow sm:rounded">
                    <Table>
                        <template #table-action>
                            <div class="flex shrink-0 rounded overflow-hidden">
                                <SelectInput class="h-9 text-sm" v-model="data.params.perPage"
                                             :dataSet="$page.props.app.perpage"/>
                                <Create v-show="can(['pegawai create'])" :title="props.title" :statuses="props.statuses" :pegawaipegawai="props.pegawaipegawai"/>
                                <DeleteBulk v-show=" data.selectedId.length != 0 && can(['pegawai delete'])
                                    "
                                    :selectedId="data.selectedId"
                                    :title="props.title"
                                    @close="
                                        (data.selectedId = []),
                                            (data.multipleSelect = false)
                                    "
                                />
                            </div>
                            <div class="flex justify-end items-center gap-2">
                                <button class="flex items-center justify-center w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-l-md hover:bg-gray-300 dark:hover:bg-gray-600"
                                        @click="toggleSearch">
                                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-950 dark:text-gray-300"/>
                                </button>
                                <TextInput v-if="showSearch" v-model="data.params.search"
                                           :placeholder="lang().placeholder.search"
                                           class="block h-9 transition-all duration-300"
                                           type="text"/>
                            </div>
                        </template>
                        <template #table-head>
                            <tr>
                                <th class="p-4 text-left w-5">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll"/>
                                </th>
                                <th class="p-4 text-center w-5">#</th>
                                <th class="p-4 cursor-pointer" v-on:click="order('user.name')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.name }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('user.email')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.email }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('user.phone_number')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.phone_number }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('status_kepegawaian')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.statusepe }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('status')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.status }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('created_at')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.created }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="p-4 text-center sr-only">Action</th>
                            </tr>
                        </template>
                        <template #table-body>
                            <tr v-for="(pegawai, index) in pegawais.data" :key="index" class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap px-2 py-1">
                                    <input class="rounded dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-slate-800 dark:checked:bg-primary dark:checked:border-primary"
                                        type="checkbox" @change="select" :value="pegawai.id"
                                        v-model="data.selectedId"/>
                                </td>
                                <td class="whitespace-nowrap px-2 py-1 text-center">{{ ++index }}</td>
                                <td class="whitespace-nowrap px-2 py-1">{{ pegawai.user.first_name }}</td>
                                <td class="whitespace-nowrap px-2 py-1">{{ pegawai.user.email }}</td>
                                <td class="whitespace-nowrap px-2 py-1">{{ pegawai.user.phone_number }}</td>
                                <td class="whitespace-nowrap px-4 py-1">{{ props.pegawaipegawai[pegawai.status_kepegawaian] || '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-1">{{ props.statuses[pegawai.status] || '-' }}</td>
                                <td class="whitespace-nowrap px-2 py-1">{{ pegawai.created_at }}</td>
                                <td class="whitespace-nowrap flex justify-end px-2 py-1">
                                    <div class="flex w-fit rounded overflow-hidden">
                                        <Edit v-show="can(['pegawai update'])" :title="props.title" :statuses="props.statuses":pegawaipegawai="props.pegawaipegawai" :pegawai="data.pegawai" @open="data.pegawai = pegawai"/>
                                        <Delete v-show="can(['pegawai delete'])" :title="props.title" :pegawai="data.pegawai"@open="data.pegawai = pegawai"/>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template #table-pagination>
                            <TablePagination :links="props.pegawais" :filters="data.params"/>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
