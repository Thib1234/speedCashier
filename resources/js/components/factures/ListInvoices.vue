<template>
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold mb-4">Factures à envoyer</h1>
            <div v-for="facture in factures" :key="facture.id"
                class="m-6 p-4 border-2 border-gray-300 rounded-lg shadow">
                <div class="flex flex-col mb-4 space-y-2">
                    <p><strong>Client :</strong> {{ facture.client.name }}</p>
                    <p><strong>Date de facture :</strong> {{ facture.date_facture }}</p>
                    <p><strong>Montant total tvac:</strong> {{ facture.sale.total_amount }} €</p>
                    <button @click="envoyerFactureParEmail(facture.id)"
                        class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md mt-3">
                        Envoyer par e-mail
                    </button>
                </div>
                <p><strong>Mail :</strong> {{ facture.client.email }} </p>
            </div>
        </div>
    </div>
</template>


<script setup>
    import {
        ref,
        onMounted
    } from 'vue';
    const factures = ref(null);
    const loadFromServer = async (url = '/api/factures/list') => {
        try {
            const response = await axios.get(url);
            factures.value = response.data.factures;
            // console.log(factures.value);
        } catch (error) {
            console.error(error);
        }
    };
    const envoyerFactureParEmail = async (id) => {
        const requestData = {
            id: id,
        }
        console.log(requestData);
        await axios.post("/api/factures/send", requestData)
            .then(response => {
                console.log(response);
            })
            .catch(error => {
                console.log(error);
            })
            loadFromServer();
    }
    onMounted(async () => {
        loadFromServer();
    });

</script>
