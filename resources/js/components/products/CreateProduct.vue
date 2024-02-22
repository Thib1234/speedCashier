<template>
    <form>

        <div class="space-y-12 p-20">
            <h1>Créer un produit</h1>

            <div class="border-b border-gray-900/10 pb-12">

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom du
                            Produit</label>
                        <div class="mt-2">
                            <input v-model="formData.name" type="text" name="name" id="first-name"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Prix</label>
                        <div class="mt-2">
                            <input v-model="formData.price" id="price" name="price" type="number" autocomplete="price"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="stock" class="block text-sm font-medium leading-6 text-gray-900">Stock</label>
                        <div class="mt-2">
                            <input v-model="formData.stock" id="stock" name="stock" type="number" autocomplete="stock"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                </div>
                
            </div>
            <button type="button" v-on:click="createProduct" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Créer un nouveau produit
                </button>
            <span v-if="success" class="w-full bg-green-500 text-white py-4 rounded-lg mt-8 focus:outline-none text-xl">Produit ajouté avec succès</span>
        </div>

    </form>

</template>

<script setup>
import {
    ref
} from 'vue'
const errors = ref({});

const formData = ref({
    name: '',
    price: '',
})
let success = ref(false);


const createProduct = async () => {
    await axios.post('/api/products', formData.value)
        .then((res) => {
            errors.value = {};
            formData.value = {};
            success.value = true;
        })
        .catch((err) => errors.value = err.response.data.errors);
}


</script>
