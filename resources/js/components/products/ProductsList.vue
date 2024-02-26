<template>
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-8">Liste des produits</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="product in products" :key="product.id" class="bg-white rounded-lg shadow-md p-6">
                <form @submit.prevent="updateProduct(product)">
                    <div class="mb-4">
                        <label for="name" class="block text-lg font-semibold mb-2">Nom du produit:</label>
                        <input type="text" v-model="product.name" id="name" class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full">
                    </div>
                    <div class="mb-4">
                        <label for="stock" class="block text-lg font-semibold mb-2">Stock:</label>
                        <input type="number" v-model="product.stock" id="stock" class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full">
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block text-lg font-semibold mb-2">Prix:</label>
                        <input type="number" v-model="product.price" id="price" class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full">
                    </div>
                    <div class="mb-4">
                        <label for="purchase_price" class="block text-lg font-semibold mb-2">Prix d'achat:</label>
                        <input type="number" v-model="product.purchase_price" id="purchase_price" class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full">
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const products = ref([]);

const loadFromServer = async () => {
    await axios.get('/api/products')
        .then((res) => products.value = res.data.data)
        .catch((e) => console.log(e));
}

const updateProduct = async (product) => {
    await axios.put(`/api/products/${product.id}`, product)
        .then((res) => console.log(res.data.message))
        .catch((e) => console.log(e));
}

loadFromServer();

</script>

<style scoped>
.container {
    max-width: 1200px;
    margin: 0 auto;
}

input[type="text"],
input[type="number"],
button {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

input[type="text"],
input[type="number"],
button,
textarea {
    border-radius: 0.375rem;
}

button {
    cursor: pointer;
}

button:hover {
    opacity: 0.8;
}
</style>
