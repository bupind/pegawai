<template>
    <div class="w-full px-2 sm:px-0">
        <TabGroup :selectedIndex="selectedIndex" @change="updateActiveTab">
            <TabList class="flex space-x-1 rounded bg-blue-700/20 p-1">
                <Tab v-for="(tab, index) in tabs" as="template" :key="tab.value" v-slot="{ selected }">

                    <button class="w-full sm:rounded-tl-md sm:rounded-tr-md py-2.5 text-sm font-medium leading-5 focus:outline-none focus:ring-2"
                        :class="[selected ? 'bg-white text-blue-700 shadow' : 'text-blue-100 hover:bg-white/[0.12] hover:text-white']"
                    >
                        {{ tab.label }}
                    </button>
                </Tab>
            </TabList>

            <TabPanels class="mt-1">
                <TabPanel v-for="(tab, index) in tabs" :key="tab.value" class="bg-white dark:bg-slate-800 shadow sm:rounded-bl-md sm:rounded-br-md p-1">
                    <slot :name="tab.value"></slot>
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>
</template>

<script setup>
import { computed, defineEmits, defineProps } from 'vue';
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/vue';

const props = defineProps({
    tabs: {
        type: Array,
        required: true,
        default: () => []
    },
    activeTab: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['update:activeTab']);

const selectedIndex = computed(() => {
    return props.tabs.findIndex(tab => tab.value === props.activeTab);
});

const updateActiveTab = (index) => {
    emit('update:activeTab', props.tabs[index].value);
};
</script>
