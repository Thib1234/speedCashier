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
                <div v-for="sale in sales" :key="sale.id" class="border p-4 mb-4">

                    Numéro de la vente {{ sale.id }}
                    <div v-for="test in sale.products" key="test.id">
                        <p>Nom du Produit : {{ test.name }}</p>
                        <p>Prix du produit : {{ test.price }} €</p>
                    </div>
                    <p><span class="font-semibold">Montant total de la vente:</span> {{ sale.payment.total_amount }} €</p>
                    
                    <button @click="deleteSale(sale.id)" class="bg-red-500 text-white px-4 py-2 mt-2">Supprimer la vente</button>
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
    const sales = ref([]);

    async function fetchSalesStats() {
        try {
            const response = await axios.get('/api/daily-stats');
            const data = response.data;
            totalSales.value = data.total_sales;
            totalClients.value = data.total_clients;
            totalPayments.value = data.total_payments;
            salesLines.value = data.sale_lines;
            sales.value = data.sales;
            await nextTick();
            loading.value = false;
            renderChart(data);
        } catch (error) {
            if (error.response) {
                console.error('Error fetching sales:', error.response.data);
            } else {
                console.error('Error fetching sales:', error);
            }
        }
    }

    onMounted(async () => {
        fetchSalesStats();
    })

    async function renderChart(data) {
        await nextTick(); // Attendre la mise à jour du DOM

        // Détruire le graphique existant s'il y en a un
        if (salesChart) {
            salesChart.destroy();
        }

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
                                callback: function (value) {
                                    return value + ' €'; // Ajouter le symbole euro
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
            console.log('Sale ID:', deletedSaleId); // Afficher le saleId dans la console
            salesLines.value = salesLines.value.filter(sale => sale.id !== deletedSaleId);
            await fetchSalesStats(); // Rappeler fetchSalesStats après la suppression
        } catch (error) {
            console.error('Erreur lors de la suppression de la vente:', error.response.data.message);
        }
    }

</script>
