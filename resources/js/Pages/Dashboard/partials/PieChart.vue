<template>
    <div className="h-100 w-100">
        <apexchart
            type="pie"
            :options="chartOptions"
            :series="series"
            @click="handleClick"
        />
    </div>
</template>

<script setup>
import {ref, watch} from 'vue'

const emit = defineEmits(['segmentClicked'])
const props = defineProps({
    data: {
        type: Array,
        default: () => [],
    }
})

const series = ref([])
const chartOptions = ref({
    chart: {
        type: 'pie',
        events: {
            dataPointSelection: (event, chartContext, config) => {
                const label = chartOptions.value.labels[config.dataPointIndex]
                emit('segmentClicked', label)
            }
        }
    },
    labels: [],
    legend: {
        position: 'bottom'
    },
    colors: ['#1D4ED8', '#FACC15', '#DC2626', '#FF6384', '#A1A1AA']
})

watch(() => props.data, (newData) => {
    if (!newData || !newData.length) return

    series.value = newData.map(d => d.value)
    chartOptions.value.labels = newData.map(d => d.label)
}, {immediate: true})
</script>
