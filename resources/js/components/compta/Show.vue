<template>
    <div class="bg-white p-4 rounded-lg shadow-md">

        {{ data }}
        {{ salesData }}

        <div v-for="stat in data" :key="stat.id" class="mb-4">
            <div class="font-semibold mb-1">Cash : {{ stat.cash }}</div>
            <div class="text-sm text-gray-600">Bancontact : {{ stat.bancontact }}</div>
            <div class="text-sm text-gray-600">Carte de crédit : {{ stat.credit_card }}</div>
            <div class="text-sm text-gray-600">Virement : {{ stat.virement }}</div>
            <div v-for="product in stat.products" :key="product.id" class="flex justify-between text-sm">
                <div class="text-gray-800">Nom article : {{ product.name }}</div>
                <div class="text-gray-600">Prix : {{ product.price }} €</div>
                <div class="text-gray-600">Qté: {{ product.pivot.quantity }}</div>
            </div>
            <h3>Montant total de la vente : {{ stat.total_amount }} €</h3>
            <hr class="my-2">
        </div>
        <div class="font-semibold text-lg mt-4">Total : {{ total_sales }} €</div>
    </div>
</template>

<script setup>
    import {
        defineProps
    } from 'vue';

    const props = defineProps({
        data: Object,
        startDate: String,
        endDate: String,
        total_sales: Number,
        salesData: Array,
    });

</script>
