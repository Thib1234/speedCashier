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
        <h2>
            <div v-if="daily">
                {{daily[0].date}}
            </div>
            
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
        </h2>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from 'vue';
    import Chart from 'chart.js/auto';

    const salesChartCanvas = ref(null);
    let salesChart = null;
    const loading = ref(false);
    const startDate = ref('');
    const endDate = ref('');
    const statsData = ref(null);
    const salesLines = ref([]);
    const sales = ref([]);
    const daily = ref(null);

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
            const data = response.data
            statsData.value = data;
            sales.value = data.sales;
            salesLines.value = data.sale_lines;
            daily.value = data.dailyTotal;
            renderChart(statsData.value);
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
                labels: ['iss', 'Nombre de clients', 'Montant total des paiements'],
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
                            callback: function (value) {
                                return value + ' €';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
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

    async function deleteSale(saleId) {
        try {
            const response = await axios.delete(`/api/sales/${saleId}`);
            const deletedSaleId = response.data.sale_id;
            console.log('Sale ID:', deletedSaleId); // Afficher le saleId dans la console
            salesLines.value = salesLines.value.filter(sale => sale.id !== deletedSaleId);
            await fetchStats(); // Rappeler fetchSalesStats après la suppression
        } catch (error) {
            console.error('Erreur lors de la suppression de la vente:', error.response);
        }
    }
</script>