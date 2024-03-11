<template>
    <div class="container mx-auto">
        <h2 class="text-3xl font-semibold mb-4">Statistiques de ventes pour aujourd'hui</h2>
        <div class="mb-4">
            <p><span class="font-semibold">Total des ventes:</span> {{ totalSales }}</p>
            <p><span class="font-semibold">Nombre de clients:</span> {{ totalClients }}</p>
            <p><span class="font-semibold">Montant total:</span> {{ totalVentes }}</p>
        </div>
        <div v-if="loading" class="text-gray-600">Chargement en cours...</div>
        <div v-else>
            <canvas ref="salesChartCanvas" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="container mx-auto">
        <h2 class="text-3xl font-semibold mb-4">Historique des ventes du jour</h2>
        <div class="mb-4">
            <div v-for="vente in salesLines" :key="vente.id">
                <p><span class="font-semibold">ID Vente:</span> {{ vente.id }}</p>
                <p><span class="font-semibold">Total:</span> {{ vente.total }}</p>
                <ul>
                    <li v-for="product in vente.products" :key="product.id">
                        {{ product.name }} - {{ product.price }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted,
        nextTick
    } from 'vue';
    import Chart from 'chart.js/auto';

    const loading = ref(true);
    const salesChartCanvas = ref(null);
    let salesChart = null;
    const totalSales = ref(0);
    const totalClients = ref(0);
    const totalVentes = ref(0);
    const salesLines = ref([]);
    
    onMounted(async () => {
        try {
            const response = await axios.get('/api/daily-stats');
            const data = response.data;
            totalSales.value = data.total_sales;
            totalClients.value = data.total_clients;
            totalVentes.value = data.total_payments;
            salesLines.value = data.sale_lines; // Assuming your backend sends sale lines with products
            await nextTick();
            loading.value = false;
            renderChart(data);
            console.log(salesLines.value);
        } catch (error) {
            console.error('Error fetching daily stats:', error.response.data.message);
        }
    });

    async function renderChart(data) {
        await nextTick(); // Wait for DOM to update
        if (salesChartCanvas.value) {
            const ctx = salesChartCanvas.value.getContext('2d');
            salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Montant total des paiements'],
                    datasets: [{
                        label: 'Montant',
                        data: [data.total_payments],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value, index, values) {
                                    return value + ' €'; // Add euro symbol
                                }
                            }
                        }
                    }
                }
            });
        }
    }
</script>

<style>
    /* Component-specific styles */
    /* You can add Tailwind CSS styles or customize as per your preference */
</style>
