<template>
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold mb-4">Détails des factures</h1>
            <div v-for="facture in factures" :key="facture.id" class="mb-4">
                <p><strong>Client :</strong> {{ facture.client.name }}</p>
                <p><strong>Date de facture :</strong> {{ facture.date_facture }}</p>
                <p><strong>Montant total HTVA :</strong> {{ facture.sale.total_amount }} €</p>
                <button @click="envoyerFactureParEmail(facture.id)"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Envoyer par e-mail
                </button>
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
            console.log(factures.value);
            console.log("lllo");

        } catch (error) {
            console.error(error);
        }
    };
    const envoyerFactureParEmail = (id) => {
        console.log(id);
    }

    onMounted(async () => {
        loadFromServer();
    });

</script>
