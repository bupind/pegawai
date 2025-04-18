<script setup>
import {Link, router} from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavbarLink from "@/Components/Guest/NavbarLink.vue";
import SwitchDarkMode from "@/Components/SwitchDarkMode.vue";
import {Bars3BottomRightIcon, XMarkIcon} from "@heroicons/vue/24/outline";
import {reactive, ref} from "vue";
import {ChevronDownIcon} from '@heroicons/vue/20/solid'

const data = reactive({
    isOpen: false,
    fixed: false,
});
const isOpen = ref(false);
window.addEventListener("scroll", () => {
    let scrollPos = window.scrollY;
    if (scrollPos > 0) {
        data.fixed = true;
    } else {
        data.fixed = false;
    }
});
const logout = () => {
    router.post(route("logout"));
};
</script>
<template>
    <header v-bind:class=" data.fixed || data.isOpen ? 'bg-white/70 dark:bg-slate-900/70 backdrop-blur-lg border-b border-slate-300/50 dark:border-slate-700/50'
                : 'border-none'
        " class="w-full fixed text-slate-600 dark:text-slate-200 z-50"
    >
        <div class="flex flex-col max-w-7xl px-4 mx-auto sm:items-center sm:justify-between sm:flex-row sm:px-6 lg:px-8 py-2 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('index')" class="shrink-0 flex w-full justify-start items-center space-x-4">
                        <ApplicationLogo class="block h-8 w-auto"/>
                        <p class="text-lg font-semibold uppercase tracking-widest">
                            {{ $page.props.app.setting.name }}
                        </p>
                    </Link>
                </div>
                <div class="sm:hidden">
                    <SwitchDarkMode/>
                    <button @click="data.isOpen = !data.isOpen"
                            class="inline-flex items-center justify-center p-2 rounded text-slate-400 dark:text-slate-500 hover:text-slate-500 dark:hover:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 focus:outline-none focus:bg-slate-100 dark:focus:bg-slate-900 focus:text-slate-500 dark:focus:text-slate-400 transition duration-150 ease-in-out">
                        <Bars3BottomRightIcon
                                v-if="!data.isOpen"
                                class="w-6 h-auto"
                        />
                        <XMarkIcon v-else class="w-6 h-auto"/>
                    </button>
                </div>
            </div>
            <nav :class="data.isOpen ? '' : 'hidden'" class="relative sm:flex items-center space-y-2 sm:space-y-0 gap-2 py-4 sm:py-0">
                <div v-if="$page.props.auth.user" class="relative">
                    <button @click="isOpen = !isOpen"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-black/20 rounded-md hover:bg-black/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
                    >
                        Dashboard
                        <ChevronDownIcon class="ml-2 h-5 w-5 text-violet-200 hover:text-violet-100" aria-hidden="true"/>
                    </button>
                    <div v-show="isOpen"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black/5 focus:outline-none divide-y divide-gray-100">
                        <a :href="route('dashboard')"
                           class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-200 rounded-md">
                            Dashboard
                        </a>
                        <a :href="route('profile.show')"
                           class="block px-4 py-2 text-sm text-gray-900 hover:bg-gray-200 rounded-md">
                            {{ lang().label.profile }}
                        </a>
                        <form @submit.prevent="logout">
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-900 hover:bg-red-500 hover:text-white rounded-md">
                                {{ lang().label.logout }}
                            </button>
                        </form>
                    </div>
                </div>
                <template v-else>
                    <NavbarLink v-if="route().has('login')" :href="route('login')" label="Login"/>
                </template>
                <SwitchDarkMode class="hidden sm:block" />
            </nav>
        </div>
    </header>
</template>
