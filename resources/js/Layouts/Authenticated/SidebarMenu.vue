<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {
    ChevronRightIcon,
    Cog6ToothIcon,
    DocumentTextIcon,
    FolderIcon,
    NewspaperIcon,
    PhotoIcon,
    QuestionMarkCircleIcon,
    SignalIcon,
    Squares2X2Icon,
    TrophyIcon,
    UserIcon
} from "@heroicons/vue/24/outline";
import {Link, usePage} from "@inertiajs/vue3";

const page = usePage();
const isRouteActive = (routeName) => route().current(routeName);
const menus = [
    {
        label: "dashboard",
        route: "dashboard",
        icon: Squares2X2Icon,
        permission: null
    },
    {
        label: "employee_management",
        icon: NewspaperIcon,
        permission: null,
        submenus: [
            {label: "employee", route: "employee.index", permission: "employee read"},
            {label: "registrationcertificate", route: "registrationcertificate.index", permission: "registrationcertificate read"},
            {label: "license", route: "license.index", permission: "license read"},
        ]
    },
    {
        label: "user_management",
        icon: UserIcon,
        permission: null,
        submenus: [
            {label: "admin", route: "admin.index", permission: "user read"},
            {label: "role", route: "role.index", permission: "role read"},
            {label: "user", route: "user.index", permission: "user read"},
            {label: "permission", route: "permission.index", permission: "permission read"}
        ]
    },
    {
        label: "setting",
        route: "setting.index",
        icon: Cog6ToothIcon,
        permission: "setting read"
    }
];
const isActiveMenu = (menu) => {
    return menu.submenus?.some(sub => isRouteActive(sub.route));
};
</script>

<template>
    <div class="flex justify-center items-center border-b border-white/10 h-16 max-w-full px-3">
        <Link :href="route('dashboard')" class="flex w-full justify-start items-center space-x-4" preserve-state
              preserve-scroll>
            <ApplicationLogo class="block h-8 w-auto"/>
            <p class="block text-lg font-semibold truncate">
                {{ $page.props.app.setting.name }}
            </p>
        </Link>
    </div>
    <ol class="pb-24 pt-5 space-y-0">
        <template v-for="menu in menus" :key="menu.label">
            <li v-if="!menu.submenus && (!menu.permission || can([menu.permission]))"
                :class="isRouteActive(menu.route) ? 'border-l-4 border-white font-semibold bg-white/20 dark:bg-primary/30' : ''"
                class="hover:bg-white/20 dark:hover:bg-primary/30">
                <Link :href="route(menu.route)" class="flex items-center py-1.5 px-3 space-x-2"
                      preserve-state preserve-scroll>
                    <component :is="menu.icon" class="w-5 h-auto"/>
                    <span>{{ lang().label[menu.label] }}</span>
                </Link>
            </li>
            <Disclosure
                v-else
                v-slot="{ open }"
                as="div"
                :default-open="isActiveMenu(menu)"
                v-if="menu.submenus && menu.submenus.some(sub => can([sub.permission]))"
            >
                <li class="hover:bg-white/20 dark:hover:bg-primary/30"
                    :class="{ 'border-l-4 border-white font-semibold bg-white/20 dark:bg-primary/30': isActiveMenu(menu) }">
                    <DisclosureButton class="flex items-center justify-between w-full py-1.5 px-3 space-x-2">
                        <div class="flex items-center space-x-2">
                            <component :is="menu.icon" class="w-5 h-auto"/>
                            <span>{{ lang().label[menu.label] }}</span>
                        </div>
                        <ChevronRightIcon class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-90': open || isActiveMenu(menu) }"/>
                    </DisclosureButton>
                    <DisclosurePanel>
                        <ul class="ml-6 space-y-1 rounded-md py-1">
                            <li v-for="sub in menu.submenus" :key="sub.route" v-show="can([sub.permission])">
                                <Link :href="route(sub.route)"
                                      class="flex items-center space-x-2 px-4 py-2 hover:bg-gray-700"
                                      :class="{ 'border-l-4 border-white font-semibold bg-white/20 dark:bg-primary/30': isRouteActive(sub.route) }"
                                      preserve-state preserve-scroll>
                                    <component :is="sub.icon || menu.icon" class="w-5 h-auto"/>
                                    <span>{{ lang().label[sub.label] }}</span>
                                </Link>
                            </li>
                        </ul>
                    </DisclosurePanel>
                </li>
            </Disclosure>
        </template>
    </ol>
</template>
