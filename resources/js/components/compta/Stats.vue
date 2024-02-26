<template>
    <div class="container mx-auto">
        <h2 class="text-3xl font-semibold mb-4">Statistiques de ventes</h2>
        <div class="mb-4">
            <label for="startDate" class="font-semibold">Date de début:</label>
            <input type="date" id="startDate" v-model="startDate">
            <label for="endDate" class="font-semibold">Date de fin:</label>
            <input type="date" id="endDate" v-model="endDate">
            <button @click="fetchStats" class="px-4 py-2 bg-blue-500 text-white rounded-md ml-4">Afficher</button>
        </div>
        <div v-if="loading" class="text-gray-600">Chargement en cours...</div>
        <div v-else>
            <canvas ref="salesChartCanvas" width="400" height="200"></canvas>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import Chart from 'chart.js/auto';

    const salesChartCanvas = ref(null);
    let salesChart = null;
    const loading = ref(false);
    const startDate = ref('');
    const endDate = ref('');

    const fetchStats = async () => {
        if (!startDate.value || !endDate.value) {
            return; // Ne rien faire si les dates ne sont pas renseignées
        }

        try {
            const response = await axios.get('/api/stats', {
                params: {
                    start: startDate.value,
                    end: endDate.value
                }
            });
            const statsData = response.data;

            renderChart(statsData);

        } catch (error) {
            console.error('Erreur lors de la récupération des statistiques :', error);
        }
    };

    function renderChart(data) {
        if (salesChart) {
            salesChart.destroy();
        }

        const ctx = salesChartCanvas.value.getContext('2d');
        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total des ventes', 'Nombre de clients', 'Montant total des paiements'],
                datasets: [{
                    label: 'Statistiques',
                    data: [data.total_sales, data.total_clients, data.total_payments],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value, index, values) {
                                return value + ' €';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(2) + ' €';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    onMounted(() => {
        fetchStats();
    });
</script>
