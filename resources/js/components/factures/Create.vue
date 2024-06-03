<template>
    <div class="m-6">
        <h1 class="text-2xl font-bold mb-6">
            Création de Facture
        </h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="sale in sales.data" :key="sale.id" class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Vente #{{ sale.id }}</h2>
                <p class="mb-2"><strong>Date:</strong> {{ new Date(sale.datetime).toLocaleDateString() }}</p>
                <p class="mb-2"><strong>Total:</strong> {{ sale.total_amount }}€</p>
                <div class="mb-4">
                    <strong>Articles:</strong>
                    <ul>
                        <li v-for="product in sale.products" :key="product.id">
                            {{ product.name }} - {{ product.pivot.quantity }} x {{ product.pivot.price }}€
                        </li>
                    </ul>
                </div>
                <button @click="selectSale(sale.id)"
                    class="bg-blue-500 text-white w-full py-2 rounded-lg hover:bg-blue-600 focus:outline-none">
                    Sélectionner cette vente
                </button>
            </div>
            <div class="flex justify-center mt-8">
                <button v-if="sales.prev_page_url" @click="loadFromServer(sales.prev_page_url)"
                    class="bg-gray-500 text-white py-2 px-4 rounded-lg mr-2">
                    Précédent
                </button>
                <button v-if="sales.next_page_url" @click="loadFromServer(sales.next_page_url)"
                    class="bg-gray-500 text-white py-2 px-4 rounded-lg">
                    Suivant
                </button>
            </div>
        </div>
    </div>
    <div v-if="isModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Associer Client et Créer Facture</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Vente ID: {{ selectedSaleId }}</p>
                    <form @submit.prevent="associateClientAndCreateFacture">
                        <div class="mb-4">
                            <label for="client" class="block text-gray-700 text-sm font-bold mb-2">Client:</label>
                            <select name="client" id="client" v-model="selectedClientId">
                                <option v-for="client in clients" :value="client.id">{{client.name}}</option>
                            </select>
                        </div>

                        <button
                            class="bg-blue-500 text-white active:bg-blue-600 font-bold uppercase text-sm px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1"
                            type="submit">
                            Associer et Créer
                        </button>
                    </form>
                </div>
                <div class="items-center px-4 py-3">
                    <button @click="closeModal" id="ok-btn"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from 'vue';
    const formData = ref({
        id: '',
    })
    const sales = ref({
        data: []
    });
    const clients = ref({
        data: []
    });
    const selectedClientId = ref(null);

    const isModalOpen = ref(false);
    const selectedSaleId = ref(null);
    const clientName = ref('');

    const selectSale = (saleId) => {
        selectedSaleId.value = saleId;
        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
    };

    const associateClientAndCreateFacture = async () => {
        console.log(selectedSaleId.value);
        console.log(selectedClientId.value);
        const requestData = {
            client_id: selectedClientId.value,
            sale_id: selectedSaleId.value
        };
        if (selectedClientId.value != null && selectedSaleId.value != null) {
            await axios.post("/api/factures/store", requestData)
                .then(response => {
                    console.log(response);
                })
                .catch(error => {
                    console.log(error);
                })

            closeModal();
        }

    };


    const loadFromServer = async (url = '/api/factures/create') => {
        try {
            const response = await axios.get(url);
            sales.value = response.data.sales;
            clients.value = response.data.clients;
            console.log(clients.value);

        } catch (error) {
            console.error(error);
        }
    };

    onMounted(async () => {
        loadFromServer();
    });

</script>
