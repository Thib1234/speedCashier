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
        <h2 v-if="total_sales">
            TOTAL : {{ total_sales }} €
            du {{ startDate }} au {{ endDate }}
        </h2>
        <div v-for="sale in sales" :key="sale.id" class="p-2 mb-2 bg-gray-50 rounded-lg shadow">
    <h3 class="text-sm font-semibold text-gray-800 mb-1">Vente: {{ sale.id }}</h3>
    <div v-for="product in sale.products" :key="product.id" class="mb-1">
        <p class="text-sm font-semibold text-gray-700">Produit: {{ product.name }}, Prix: {{ product.price }}€, Quantité: {{ product.pivot.quantity }}, Total: {{ product.pivot.total }}€</p>
    </div>
    <p class="text-sm font-semibold text-gray-700">Total de la vente: {{ sale.total_amount }}€</p>
    <button @click="deleteSale(sale.id)" class="mt-2 bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline text-sm">Supprimer</button>
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
            total_sales.value = data.total_sales;
            salesByDay.value = data.salesByDay;
            console.log(sales.value);
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
        labels: Object.keys(data.salesByDay), // Utiliser les dates comme étiquettes
        datasets: [{
            label: 'Total des ventes par jour',
            data: Object.values(data.salesByDay), // Utiliser les totaux des ventes par jour comme données
            backgroundColor: 'rgba(54, 162, 235, 0.6)', // Couleur de remplissage plus douce
            borderColor: 'rgba(54, 162, 235, 1)', // Couleur de la bordure
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
