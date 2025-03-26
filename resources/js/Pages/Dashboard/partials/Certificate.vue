<script setup>
import Table from "@/Components/Table.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TablePagination from "@/Components/TablePagination.vue";
import TextInput from "@/Components/TextInput.vue";
import { reactive, ref, watch } from "vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
import { ChevronUpDownIcon, MagnifyingGlassIcon } from "@heroicons/vue/24/outline";

const { _, debounce, pickBy } = pkg;

const props = defineProps({
    filters: Object,
    certificateTable: Object,
    types: Object,
    statuses: Object,
    perPage: Number,
});

const showSearch = ref(false);
const toggleSearch = () => (showSearch.value = !showSearch.value);

const data = reactive({
    params: {
        search: props.filters.search || "",
        field: props.filters.field || "created_at",
        order: props.filters.order || "desc",
        perPage: props.perPage || 10,
    },
});

const order = (field) => {
    data.params.field = field;
    data.params.order = data.params.order === "asc" ? "desc" : "asc";
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        router.get(route("dashboard.certificates"), pickBy(data.params), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
    }, 150)
);
</script>

<template>
    <Table class="shadow-md rounded-md mt-4">
        <template #table-action>
            <div class="flex shrink-0 rounded overflow-hidden">
                <SelectInput v-model="data.params.perPage" :dataSet="$page.props.app.perpage"
                             class="h-9 text-sm px-3" />
            </div>
            <div class="flex items-center space-x-2">
                <div class="flex space-x-0">
                    <button class="flex items-center justify-center w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-l-md hover:bg-gray-300 dark:hover:bg-gray-600"
                            @click="toggleSearch">
                        <MagnifyingGlassIcon class="w-5 h-5 text-gray-950 dark:text-gray-300" />
                    </button>
                    <TextInput v-if="showSearch" v-model="data.params.search" :placeholder="lang().placeholder.search"
                               class="block h-9 w-36 transition-all duration-300" type="text" />
                </div>
            </div>
        </template>

        <template #table-head>
            <tr>
                <th class="p-4 text-center w-5">#</th>
                <th class="p-4 cursor-pointer" @click="order('employeeId')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.employee }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('type')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.type }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('registrationNumber')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.registrationNumber }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('competence')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.competence }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('certificateOfCompetenceNumber')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.certificateOfCompetenceNumber }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('validFrom')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.validFrom }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('validUntil')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.validUntil }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('status')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.status }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
                <th class="p-4 cursor-pointer" @click="order('created_at')">
                    <div class="flex justify-between items-center">
                        <span>{{ lang().label.created_at }}</span>
                        <ChevronUpDownIcon class="w-4 h-4" />
                    </div>
                </th>
            </tr>
        </template>

        <template #table-body>
            <template v-if="certificateTable.data?.length">
                <tr v-for="(data, index) in certificateTable.data" :key="data.id"
                    class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                    <td class="whitespace-nowrap px-2 py-1 text-center">{{ index + 1 }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ data.employee?.name || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ props.types[data.type] || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ data.registrationNumber || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ data.competence || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ data.certificateOfCompetenceNumber || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ data.validFrom || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ data.validUntil || '-' }}</td>
                    <td class="whitespace-nowrap px-4 py-1">{{ props.statuses[data.status] || '-' }}</td>
                    <td class="px-4 py-1">{{ data.created_at || '-' }}</td>
                </tr>
            </template>
            <tr v-else>
                <td colspan="3" class="text-center py-4 text-gray-500 dark:text-gray-400">
                    {{ lang().label.no_data }}
                </td>
            </tr>
        </template>

        <template #table-pagination>
            <TablePagination :filters="data.params" :links="props.certificateTable" />
        </template>
    </Table>
</template>
