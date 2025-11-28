<script setup>
import { onMounted, ref, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    labels: Array,
    datasets: Array,
    title: String,
    horizontal: {
        type: Boolean,
        default: false
    }
});

const chartRef = ref(null);
let chartInstance = null;

const createChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartRef.value.getContext('2d');
    chartInstance = new Chart(ctx, {
        type: props.horizontal ? 'bar' : 'bar',
        data: {
            labels: props.labels,
            datasets: props.datasets.map((dataset, index) => ({
                label: dataset.label,
                data: dataset.data,
                backgroundColor: dataset.backgroundColor || `rgba(${59 + index * 40}, ${130 + index * 30}, ${246 - index * 40}, 0.6)`,
                borderColor: dataset.borderColor || `rgba(${59 + index * 40}, ${130 + index * 30}, ${246 - index * 40}, 1)`,
                borderWidth: 2
            }))
        },
        options: {
            indexAxis: props.horizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: !!props.title,
                    text: props.title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y || context.parsed.x || 0;
                            return `${label}: ${value.toFixed(2)}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toFixed(0);
                        }
                    }
                }
            }
        }
    });
};

onMounted(() => {
    createChart();
});

watch(() => [props.labels, props.datasets], () => {
    createChart();
}, { deep: true });
</script>

<template>
    <div class="relative">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
