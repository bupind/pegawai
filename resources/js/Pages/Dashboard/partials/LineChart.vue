<template>
    <div className="h-64">
        <apexchart
                type="line"
                :options="chartOptions"
                :series="series"
                height="100%"
        />
    </div>
</template>

<script setup>
import {ref, watch} from "vue"

const props = defineProps({
    data: Array
})

const baseColors = [
    "#36A2EB", "#FF6384", "#4BC0C0", "#FFCE56", "#9966FF",
    "#FF9F40", "#C9CBCF", "#00A676", "#B5A642", "#8A89A6"
]

const series = ref([])
const chartOptions = ref({
    chart: {
        id: 'lineChart',
        zoom: {enabled: false},
        toolbar: {show: false}
    },
    stroke: {
        curve: 'smooth',
        width: 2
    },
    markers: {
        size: 4,
        hover: {sizeOffset: 2}
    },
    xaxis: {
        type: 'category',
        categories: [],
        title: {text: 'Tanggal'}
    },
    yaxis: {
        title: {text: ''},
        min: 0,
        forceNiceScale: true,
        tickAmount: 5
    },
    tooltip: {
        enabled: true
    },
    legend: {
        position: 'top'
    },
    colors: baseColors
})

watch(() => props.data, (newData) => {
    if (!newData || !newData.length) return

    chartOptions.value.xaxis.categories = newData[0].data.map(d => d.date)

    series.value = newData.map((category, index) => ({
        name: category.name,
        data: category.data.map(d => d.total)
    }))
}, {immediate: true})
</script>
