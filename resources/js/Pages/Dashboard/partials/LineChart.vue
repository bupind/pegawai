<template>
    <div className="chart-container">
        <LineChart v-if="chartData" :chart-data="chartData" :chart-options="chartOptions"/>
    </div>
</template>

<script setup>
import {computed} from "vue";
import {LineChart} from "vue-chart-3";
import {Chart, registerables} from "chart.js";

Chart.register(...registerables);

const props = defineProps({
    data: Array
});
const colors = [
    "#36A2EB", "#FF6384", "#4BC0C0", "#FFCE56", "#9966FF",
    "#FF9F40", "#C9CBCF", "#00A676", "#B5A642", "#8A89A6"
];

const chartData = computed(() => {
    if (!props.data || !props.data.length) return null;

    return {
        labels: props.data[0]?.data.map(d => d.date) || [],
        datasets: props.data.map((category, index) => ({
            label: category.name,
            data: category.data.map(d => d.total),
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length] + "33",
            tension: 0.1,
            pointRadius: 4,
            pointHoverRadius: 6
        }))
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: true, position: "top" },
        tooltip: { enabled: true }
    },
    scales: {
        x: {
            title: { display: true, text: "Tanggal" },
            grid: { display: false }
        },
        y: {
            title: { display: true, text: "Total Submissions" },
            beginAtZero: true,
            suggestedMax: 5,
            grid: { color: "#e5e7eb" },
            ticks: {
                stepSize: 1,   // Pastikan hanya bilangan bulat
                precision: 0   // Hindari angka desimal
            }
        }
    }
};
</script>
