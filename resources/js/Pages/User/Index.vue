<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Table from "@/Components/Table.vue";
import Breadcrumb from "@/Layouts/Authenticated/Breadcrumb.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TablePagination from "@/Components/TablePagination.vue";
import TextInput from "@/Components/TextInput.vue";
import Create from "@/Pages/User/Create.vue";
import Edit from "@/Pages/User/Edit.vue";
import Delete from "@/Pages/User/Delete.vue";
import DeleteBulk from "@/Pages/User/DeleteBulk.vue";
import {reactive, ref, watch} from "vue";
import pkg from "lodash";
import {router} from "@inertiajs/vue3";
import {CheckBadgeIcon} from "@heroicons/vue/24/solid";
import {ChevronUpDownIcon, MagnifyingGlassIcon} from "@heroicons/vue/24/outline";
import Checkbox from "@/Components/Checkbox.vue";

const {_, debounce, pickBy} = pkg;
const props = defineProps({
    title: String,
    filters: Object,
    users: Object,
    breadcrumbs: Object,
    perPage: Number,
});
const showSearch = ref(false);
const toggleSearch = () => {
    showSearch.value = !showSearch.value;
};
const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    user: null,
});

const order = (field) => {
    data.params.field = field;
    data.params.order = data.params.order === "asc" ? "desc" : "asc";
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("user.index"), params, {
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
        props.users?.data.forEach((user) => {
            data.selectedId.push(user.id);
        });
    }
};
const select = () => {
    if (props.users?.data.length == data.selectedId.length) {
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
                                <Create v-show="can(['user create'])" :title="props.title"
                                />
                                <DeleteBulk v-show=" data.selectedId.length != 0 && can(['user delete'])"
                                            :selectedId="data.selectedId"
                                            :title="props.title"
                                            @close="
                                        (data.selectedId = []), (data.multipleSelect = false)
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
                                </div>
                            </div>
                        </template>
                        <template #table-head>
                            <tr>
                                <th class="p-4 text-left w-5">
                                    <Checkbox v-model:checked="data.multipleSelect" @change="selectAll"/>
                                </th>
                                <th class="p-4 text-center w-5">#</th>
                                <th class="p-4 cursor-pointer" v-on:click="order('name')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.name }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('email')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.email_verified_at }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('phone_number')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.phone_number }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('email')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.email }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 cursor-pointer" v-on:click="order('created_at')">
                                    <div class="flex justify-between items-center">
                                        <span>{{ lang().label.created }}</span>
                                        <ChevronUpDownIcon class="w-4 h-4"/>
                                    </div>
                                </th>
                                <th class="p-4 text-center ">Action</th>
                            </tr>
                        </template>
                        <template #table-body>
                            <tr v-for="(user, index) in users.data" :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap px-4 py-1">
                                    <input
                                            v-model="data.selectedId"
                                            :value="user.id"
                                            class="rounded dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-slate-800 dark:checked:bg-primary dark:checked:border-primary"
                                            type="checkbox"
                                            @change="select"
                                    />
                                </td>
                                <td class="whitespace-nowrap px-4 py-1 text-center">
                                    {{ ++index }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-1">
                                    {{ user.first_name }} {{ user.last_name }}
                                </td>
                                <td class="whitespace-nowrap truncate px-4 py-1 text-center">
                                    <CheckBadgeIcon v-if="user.email_verified_at"
                                                    v-tooltip="'Verified at ' + user.email_verified_at"
                                                    class="w-4 h-auto text-blue-500 ml-1 text-center shrink-0"/>
                                </td>
                                <td class="whitespace-nowrap truncate px-4 py-1">
                                    {{ user.phone_number }}
                                </td>
                                <td class="whitespace-nowrap truncate px-4 py-1">
                                    {{ user.email }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-1">
                                    {{ user.created_at }}
                                </td>
                                <td class="whitespace-nowrap flex justify-end px-4 py-1">
                                    <div class="flex w-fit rounded overflow-hidden">
                                        <Edit v-show="can(['user update'])"
                                              :title="props.title"
                                              :user="data.user"
                                              @open="data.user = user"
                                        />
                                        <Delete v-show="can(['user delete'])"
                                                :title="props.title"
                                                :user="data.user"
                                                @open="data.user = user"
                                        />
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template #table-pagination>
                            <TablePagination
                                    :filters="data.params"
                                    :links="props.users"
                            />
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
