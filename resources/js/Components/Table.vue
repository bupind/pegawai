<script setup>
</script>
<template>
    <div class="bg-white dark:bg-slate-800 overflow-hidden shadow sm:rounded">
        <div class="flex justify-between items-center px-4 py-2 space-x-2 overflow-x-auto">
            <slot name="table-action"/>
        </div>
        <div class="overflow-x-auto scrollbar-table">
            <table class="w-full kv-grid-table table table-hover table-bordered table-striped table-condensed kv-table-wrap">
                <thead class="capitalize text-sm border-t border-slate-200 dark:border-slate-700 bg-blue-500 text-white">
                <slot name="table-head"/>
                </thead>
                <tbody>
                <slot name="table-body"/>
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center px-4 py-2 border-t border-slate-200 dark:border-slate-700 overflow-auto">
            <slot name="table-pagination"/>
        </div>
    </div>
</template>
