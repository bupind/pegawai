<script setup></script>
<template>
    <footer class="sticky top-[100vh]">
        <div class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
            <div class="flex justify-start items-center px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-slate-600 dark:text-slate-400">
                    {{ $page.props.app.setting.name }} &copy; {{ new Date().getFullYear() }}.
                </p>
            </div>
        </div>
    </footer>
</template>
