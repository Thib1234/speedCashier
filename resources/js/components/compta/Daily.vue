<template>
    <div class="container mx-auto">
        <h2 class="text-3xl font-semibold mb-4">Statistiques de ventes pour aujourd'hui</h2>
        <div v-if="loading" class="text-gray-600">Chargement en cours...</div>
        <div v-else>
            <div class="mb-8">
                <p>Total des ventes : {{ totalSales }}</p>
                <p>Nombre de clients : {{ totalClients }}</p>
            </div>
            <canvas ref="salesChartCanvas" width="600" height="300"></canvas>
        </div>
    </div>
</template>

<script setup>
    import Chart from 'chart.js/auto';
    import {
        ref,
        onMounted,
        nextTick
    } from 'vue';


    const loading = ref(true);
    const salesChartCanvas = ref(null);
	const totalSales = ref(0);
	const totalClients = ref(0);
	const data = null()

    onMounted(async () => {
        try {
            const response = await axios.get('/api/daily-stats');
            data.value = response.data;
            loading.value = false;
            await nextTick();
            renderChart(data.value);
        } catch (error) {
            console.error('Error fetching daily stats:', error);
        }
    });

    async function renderChart(data) {
        if (salesChartCanvas.value) {
            const ctx = salesChartCanvas.value.getContext('2d');
            new Chart(ctx, {
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
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

</script>

<style>
    /* Styles spécifiques au composant Daily.vue */
    /* Vous pouvez ajouter des styles Tailwind CSS ou personnaliser selon vos préférences */

</style>
