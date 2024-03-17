<template>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <!-- Statistiques de ventes -->
        <h2 class="text-4xl font-bold text-gray-800 mb-6">Statistiques de ventes pour aujourd'hui</h2>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-blue-100 rounded-lg shadow">
                <p class="font-semibold text-gray-700">Total des ventes:</p>
                <p class="text-xl">{{ totalSales }}</p>
            </div>
            <div class="p-4 bg-green-100 rounded-lg shadow">
                <p class="font-semibold text-gray-700">Nombre de clients:</p>
                <p class="text-xl">{{ totalClients }}</p>
            </div>
            <div class="p-4 bg-red-100 rounded-lg shadow">
                <p class="font-semibold text-gray-700">Montant total:</p>
                <p class="text-xl">{{ totalPayments }}€</p>
            </div>
        </div>

        <!-- Chargement en cours ou graphique -->
        <div v-if="loading" class="animate-pulse text-gray-600">Chargement en cours...</div>
        <div v-else>
            <canvas ref="salesChartCanvas" class="w-full h-64"></canvas>
        </div>

        <!-- Lignes de vente -->
        <div class="mt-10">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">Lignes de vente du jour</h2>
            <div v-if="salesLines.length === 0" class="text-gray-600">
                <p>Aucune vente pour aujourd'hui.</p>
            </div>
            <div v-else>
                <div v-for="sale in sales" :key="sale.id" class="p-6 mb-4 bg-gray-50 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Numéro de la vente: {{ sale.id }}</h3>
                    <div v-for="product in sale.products" :key="product.id" class="mb-2">
                        <p class="font-semibold text-gray-700">Nom du Produit:</p>
                        <p>{{ product.name }}</p>
                        <p class="font-semibold text-gray-700">Prix du produit:</p>
                        <p>{{ product.price }}€</p>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Détails du paiement:</h4>
                        <div v-if="sale.payment.cash" class="flex items-center space-x-2">
                            <span class="font-medium">Espèces:</span>
                            <span>{{ sale.payment.cash }}€</span>
                        </div>
                        <div v-if="sale.payment.bancontact" class="flex items-center space-x-2">
                            <span class="font-medium">Bancontact:</span>
                            <span>{{ sale.payment.bancontact }}€</span>
                        </div>
                        <div v-if="sale.payment.credit_card" class="flex items-center space-x-2">
                            <span class="font-medium">Carte de crédit:</span>
                            <span>{{ sale.payment.credit_card }}€</span>
                        </div>
                        <div v-if="sale.payment.virement" class="flex items-center space-x-2">
                            <span class="font-medium">Virement:</span>
                            <span>{{ sale.payment.virement }}€</span>
                        </div>
                    </div>
                    <button @click="deleteSale(sale.id)"
                        class="mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Supprimer
                        la vente</button>
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
