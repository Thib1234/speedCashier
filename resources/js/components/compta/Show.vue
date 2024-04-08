<template>
  <div class="p-4">
    <h2 class="text-lg font-bold text-center mb-4">Statistiques du {{ startDate }} au {{ endDate }}</h2>
    <div class="flex flex-wrap -mx-2">
      <div v-for="stat in data" :key="stat.id" class="w-full px-2 mb-4">
        <div class="border p-2">
          <div class="text-sm font-semibold">Vente n°{{ stat.id }} - Du {{ stat.datetime }} -  Total : {{ stat.total_amount }} €</div>
          <div v-for="product in stat.products" :key="product.id" class="flex justify-between text-xs">
            <div>{{ product.name }}</div>
            <div>{{ product.price }} €</div>
            <div>Qté: {{ product.pivot.quantity }}</div>
          </div>
          <div class="bg-gray-100 p-2 rounded">
            <div class="grid grid-cols-4 gap-2 text-xs font-medium">
              <div>Cash: {{ stat.cash }} €</div>
              <div>Bancontact: {{ stat.bancontact }} €</div>
              <div>Crédit: {{ stat.credit_card }} €</div>
              <div>Virement: {{ stat.virement }} €</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="text-sm font-semibold">Total des ventes : {{ total_sales }} €</div>
    <div class="text-sm">Total HTVA : {{ totalSalesHtva }} €</div>
    <div class="text-sm">Montant TVA : {{ totalHtva }} €</div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  data: Object,
  startDate: String,
  endDate: String,
  total_sales: Number,
  totalSalesHtva: Number,
  totalHtva: Number
});
</script>
