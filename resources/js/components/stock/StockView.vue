<template>
    <div class="p-4 text-lg">
		<h1 class="text-3xl font-bold text-center my-4">Stock Actuel</h1>
        <div class="p-4 border-b border-gray-300 flex justify-between font-bold bg-gray-100">
            <h2 class="w-1/3">Nom du produit</h2>
            <p class="w-1/3">Prix d'achat HTVA</p>
            <p class="w-1/3">Stock</p>
        </div>
        <div v-for="product in products" :key="product.id" class="p-4 border-b border-gray-300 flex justify-between text-sm">
            <h2 class="w-1/3 font-bold">{{ product.name }}</h2>
            <p class="w-1/3 text-gray-700">{{ product.purchase_price }} €</p>
            <p class="w-1/3 text-gray-700">{{ product.stock }}</p>
        </div>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from 'vue';

    const products = ref([]);

    onMounted(async () => {
        try {
            const response = await axios.get('/api/productsWithoutToilettage');
            products.value = response.data.data;
            console.log(products.value);
        } catch (error) {
            console.error('Erreur lors de la récupération des données:', error);
        }
    });

</script>

<style>
    @media print {
        .border-b {
            border-bottom-width: 1px;
        }

        .text-lg {
            font-size: 0.875rem;
            /* 18px */
        }

        .font-bold {
            font-weight: 700;
        }

        .text-gray-700 {
            color: #4a5568;
        }

        .p-4 {
            padding: 1rem;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        } 
    }

</style>
