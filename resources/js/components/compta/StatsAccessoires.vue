<template>
    <div class="container mx-auto">
        <h2 class="text-3xl font-semibold mb-4">Statistiques de ventes (uniquement toilettages)</h2>
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

        <div v-if="total_sales">
            <a :href="`/stats-toilettage/print?start=${startDate}&end=${endDate}`" class="flex-grow">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out transform hover:-translate-y-1">
                    Imprimer statistiques
                </button>
            </a>

            <h2>
                TOTAL : {{ chien }} €
                du {{ startDate }} au {{ endDate }}
            </h2>
        </div>
        <div v-for="sale in sales" :key="sale.id" class="p-2 mb-2 bg-gray-50 rounded-lg shadow">
            {{ sale }}
            <h3 class="text-sm font-semibold text-gray-800 mb-1">Vente: {{ sale.id }}</h3>
            <div v-for="product in sale.products" :key="product.id" class="mb-1">
                <p class="text-sm font-semibold text-gray-700">Produit: {{ product.name }}, Prix: {{ product.price }}€,
                    Quantité: {{ product.pivot.quantity }}, Total: {{ product.pivot.total }}€</p>
            </div>
            <p class="text-sm font-semibold text-gray-700">Total de la vente: {{ sale.total_amount }}€</p>
            <button @click="deleteSale(sale.id)"
                class="mt-2 bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline text-sm">Supprimer</button>
        </div>
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
    const total_sales = ref(null);
    const salesByDay = ref(null);
    const chien = ref(null);

    const fetchStats = async () => {
        if (!startDate.value || !endDate.value) {
            return; // Ne rien faire si les dates ne sont pas renseignées
        }
        try {
            const response = await axios.get('/api/stats-accessoires', {
                params: {
                    start: startDate.value,
                    end: endDate.value
                }
            });
            const data = response.data
			console.log(data);
            statsData.value = data;
            sales.value = data.sales;
            salesLines.value = data.sale_lines;
            daily.value = data.dailyTotal;
            total_sales.value = data.total_sales;
            salesByDay.value = data.salesByDay;
            chien.value = data.chien;
            console.log(salesByDay.value);
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
                labels: Object.keys(data.salesByDay),
                datasets: [{
                    label: 'Total des ventes par jour',
                    data: Object.values(data
                        .salesByDay),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
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
            console.log('Sale ID:', deletedSaleId);
            salesLines.value = salesLines.value.filter(sale => sale.id !== deletedSaleId);
            await fetchStats();
        } catch (error) {
            console.error('Erreur lors de la suppression de la vente:', error.response);
        }
    }
</script>
