<!-- src/Components/Charts/PieChart.vue -->
<template>
    <div class="h-64">
        <DoughnutChart v-if="chartData" :chart-data="chartData" :chart-options="chartOptions"/>
    </div>
</template>

<script setup>
import {ref, watch} from "vue";
import {DoughnutChart} from "vue-chart-3";
import {Chart, registerables} from "chart.js";

Chart.register(...registerables);

const props = defineProps({
    data: Array
});

const chartData = ref(null);
const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {position: "bottom"}
    }
});

watch(() => props.data, (newData) => {
    if (newData && newData.length) {
        chartData.value = {
            labels: newData.map(d => d.label),
            datasets: [
                {
                    data: newData.map(d => d.value),
                    backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"]
                }
            ]
        };
    }
}, {immediate: true});
</script>
