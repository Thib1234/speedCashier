<template>
    <div class="container mx-auto">
        <!-- Statistiques de ventes -->
        <h2 class="text-3xl font-semibold mb-4">Statistiques de ventes pour aujourd'hui</h2>
        <div class="mb-4">
            <p><span class="font-semibold">Total des ventes:</span> {{ totalSales }}</p>
            <p><span class="font-semibold">Nombre de clients:</span> {{ totalClients }}</p>
            <p><span class="font-semibold">Montant total:</span> {{ totalPayments }}</p>
        </div>

        <!-- Chargement en cours ou graphique -->
        <div v-if="loading" class="text-gray-600">Chargement en cours...</div>
        <div v-else>
            <canvas ref="salesChartCanvas" width="400" height="200"></canvas>
        </div>

        <!-- Lignes de vente -->
        <div class="mt-8">
            <h2 class="text-3xl font-semibold mb-4">Lignes de vente du jour</h2>
            <div v-if="salesLines.length === 0">
                <p>Aucune vente pour aujourd'hui.</p>
            </div>
            <div v-else>
                <div v-for="sale in salesLines" :key="sale.id" class="border p-4 mb-4">
                    <p><span class="font-semibold">ID Vente:</span> {{ sale.id }}</p>
                    <p><span class="font-semibold">Nom du produit:</span> {{ sale.product_name }}</p>
                    <p><span class="font-semibold">Quantité:</span> {{ sale.quantity }}</p>
                    <p><span class="font-semibold">Prix:</span> {{ sale.price }}</p>
                    <!-- Ajoutez d'autres détails de vente si nécessaire -->
                    <button @click="deleteSale(sale.id)" class="bg-red-500 text-white px-4 py-2 mt-2">Supprimer la
                        vente</button>
                </div>
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
    const totalPayments = ref(0);
    const salesLines = ref([]);


    onMounted(async () => {
        try {
            const response = await axios.get('/api/daily-stats');
            const data = response.data;
            totalSales.value = data.total_sales;
            totalClients.value = data.total_clients;
            totalPayments.value = data.total_payments;
            salesLines.value = data.sale_lines; // Mettez à jour salesLines avec les données de l'API
            await nextTick();
            loading.value = false;
            renderChart(data);
            console.log(salesLines.value); // Assurez-vous que salesLines est mis à jour correctement
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
    async function deleteSale(saleId) {
        try {
            const response = await axios.delete(`/api/sales/${saleId}`);
            const deletedSaleId = response.data.sale_id;
            // Mettre à jour l'affichage en supprimant la vente de la liste
            salesLines.value = salesLines.value.filter(sale => sale.id !== deletedSaleId);
        } catch (error) {
            console.error('Erreur lors de la suppression de la vente:', error.response.data.message);
        }
    }
    async function deleteSale(saleId) {
        try {
            const response = await axios.delete(`/api/sales/${saleId}`);
            const deletedSaleId = response.data.sale_id;
            salesLines.value = salesLines.value.filter(sale => sale.id !== deletedSaleId);
        } catch (error) {
            console.error('Erreur lors de la suppression de la vente:', error.response.data.message);
        }
    }

</script>

<style>
    /* Component-specific styles */
    /* You can add Tailwind CSS styles or customize as per your preference */

</style>
