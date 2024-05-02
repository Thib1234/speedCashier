<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Créer un nouvel acompte</h2>
        <form @submit.prevent="submitAcompte">
            <div class="mb-4">
                <label for="client" class="block text-gray-700 text-sm font-bold mb-2">Sélectionnez un client:</label>
                <select id="client" v-model="acompte.client_id"
                    class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
                    required>
                    <option disabled value="">Veuillez sélectionner un client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                </select>
            </div>
            <div class="mb-6">
                <label for="montant" class="block text-gray-700 text-sm font-bold mb-2">Montant:</label>
                <input type="number" id="montant" v-model="acompte.montant"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>
            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Créer Acompte
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from 'vue';

    const acompte = ref({
        client_id: '',
        montant: ''
    });

    const clients = ref([]);

    const submitAcompte = async () => {
        try {
            const response = await axios.post('/api/acomptes/store', acompte.value);
            alert('Acompte créé avec succès: ' + response.data.acompte_id);
            // Réinitialiser le formulaire ou gérer la suite des actions
        } catch (error) {
            console.error('Erreur lors de la création de l\'acompte:', error);
            alert('Erreur lors de la création de l\'acompte.');
        }
    };

    onMounted(async () => {
        try {
            const response = await axios.get('/api/clients');
            clients.value = response.data.data;
        } catch (error) {
            console.error('Erreur lors de la récupération des clients:', error);
        }
    });

</script>

<style scoped>
    .container {
        max-width: 600px;
    }

</style>
