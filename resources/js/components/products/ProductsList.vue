<template>
    <div>
        <h1 class="text-3xl font-bold mb-4">Liste des produits</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="product in products" :key="product.id" class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-2">{{ product.name }}</h2>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref
} from "vue"

const products = ref([]);

const loadFromServer = async () => {
    await axios.get('/api/products')
        .then((res) => products.value = res.data.data)
        .catch((e) => console.log(e))
}

loadFromServer();

</script>