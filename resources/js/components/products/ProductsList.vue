<template>
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-8">Liste des produits</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="product in products" :key="product.id" class="bg-white rounded-lg shadow-md p-6">
                <form @submit.prevent="updateProduct(product)">
                    <div class="mb-4">
                        <div class="grid grid-cols-2">
                            <label for="name" class="block text-lg font-semibold mb-2">Nom du produit:</label>
                            <button type="button" class="flex justify-between items-center"
                                @click="toggleActiveProduct(product)">
                                <div class="w-16 h-10 flex items-center bg-gray-300 rounded-full p-1 duration-300 ease-in-out"
                                    :class="{ 'bg-green-400': product.active === 1 }">
                                    <div class="bg-white w-8 h-8 rounded-full shadow-md transform duration-300 ease-in-out"
                                        :class="{ 'translate-x-6': product.active === 1 }"></div>
                                </div>
                            </button>
                        </div>
                        <input type="text" v-model="product.name" id="name"
                            class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full">
                    </div>
                    <div class="mb-4">
                        <label for="stock" class="block text-lg font-semibold mb-2">Stock:</label>
                        <input inputmode="numeric" v-model="product.stock" id="stock"
                            class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full"
                            type="number">
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block text-lg font-semibold mb-2">Prix:</label>
                        <input inputmode="decimal" v-model="product.price" id="price"
                            class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full" step=".01"
                            type="number">
                    </div>
                    <div class="mb-4">
                        <label for="purchase_price" class="block text-lg font-semibold mb-2">Prix d'achat:</label>
                        <input inputmode="decimal" v-model="product.purchase_price" id="purchase_price"
                            class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full" step=".01"
                            type="number">
                    </div>
                    <div class="mb-4">
                        <label for="category" class="block text-lg font-semibold mb-2">Catégorie:</label>
                        <select v-model="product.category_id" id="category"
                            class="border-b border-gray-400 focus:outline-none rounded-lg px-4 py-2 w-full">
                            <option value="" disabled>Sélectionner une catégorie</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Enregistrer</button>
                </form>
            </div>
        </div>
        <div v-if="success" class="fixed top-5 right-5 bg-green-200 text-green-800 text-sm px-4 py-3 rounded">
            Produit mis à jour avec succès
        </div>
        <div v-if="successToggleActive"
            class="fixed top-5 right-5 bg-green-200 text-green-800 text-sm px-4 py-3 rounded">
            Produit activé
        </div>
        <div v-if="successToggleDisabled" class="fixed top-5 right-5 bg-red-200 text-red-800 text-sm px-4 py-3 rounded">
            Produit désactivé
        </div>
    </div>
</template>

<script setup>
    import {
        ref
    } from "vue";
    const products = ref([]);
    const categories = ref([]);
    const success = ref(false);
    const successToggleActive = ref(false);
    const successToggleDisabled = ref(false);

    const loadFromServer = async () => {
        try {
            const response = await axios.get('/api/products');
            products.value = response.data.products.data;
            categories.value = response.data.categories.data;
        } catch (error) {
            console.error(error);
        }
    };
    const updateProduct = async (product) => {
        try {
            await axios.put(`/api/products/${product.id}`, product);
            console.log("Product updated successfully");
            success.value = true;
            setTimeout(() => {
                success.value = false;
            }, 3000);
        } catch (error) {
            console.error(error);
        }
    };
    const updateActive = async (product) => {
        try {
            await axios.put(`/api/activeProduct/${product.id}`, product);
            if (product.active) {
                successToggleActive.value = true
            } else {
                successToggleDisabled.value = true
            }
            setTimeout(() => {
                successToggleDisabled.value = false;
                successToggleActive.value = false;
            }, 3000);
        } catch (error) {
            console.error(error);
        }
    };
    const toggleActiveProduct = async (product) => {
        product.active = product.active === 0 ? 1 : 0;
        await updateActive(product);
    };
    loadFromServer();

</script>
