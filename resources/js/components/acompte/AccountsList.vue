<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Acomptes en cours</h2>
        <ul class="bg-white rounded-lg border border-gray-200 w-full p-4 divide-y divide-gray-200">
            <li v-for="acompte in acomptes" :key="acompte.id" @click="showModal(acompte)"
                class="flex justify-between items-center p-3 hover:bg-gray-100 cursor-pointer">
                <span>Client ID: {{ acompte.client_id }}</span>
                <span>Montant: {{ acompte.montant }}€</span>
                <span>Status: {{ acompte.status }}</span>
            </li>
        </ul>
    </div>
    <div v-if="modalVisible"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg p-5">
            <div class="flex justify-between items-center">
                <h3 class="text-lg">Valider Acompte</h3>
                <button class="text-gray-600" @click="modalVisible = false">&times;</button>
            </div>
            <p class="my-4">Changer le statut de l'acompte pour : {{ selectedAcompte.client_id }}</p>
            <button @click="validateAcompte"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Valider
            </button>
        </div>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from 'vue';

    const acomptes = ref([]);
    const modalVisible = ref(false);
    const selectedAcompte = ref(null);
    const showModal = (acompte) => {
        selectedAcompte.value = acompte;
        modalVisible.value = true;
    };

    const validateAcompte = async () => {
    try {
        const response = await axios.post(`/api/account/${selectedAcompte.value.id}/apply-acompte`, {
            status: 'validé'
        });
        selectedAcompte.value.status = 'validé'; // Mettre à jour localement si l'API est réussie
        console.log('Statut mis à jour:', response.data);

        // Créer une vente
        const saleData = {
            client_id: selectedAcompte.value.client_id,
            total_amount: selectedAcompte.value.montant,
            // Ajoutez d'autres champs nécessaires pour la vente ici
        };
        const saleResponse = await axios.post('/api/sale/store', saleData); // Remplacez '/api/sale/store' par l'URL de votre API pour créer une vente
        console.log('Vente créée avec succès:', saleResponse.data);

        modalVisible.value = false;
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut ou de la création de la vente:', error);
    }
};


    onMounted(async () => {
        try {
            const response = await axios.get('/api/acomptes');
            acomptes.value = response.data;
            console.log(response);
        } catch (error) {
            console.error('Il y a eu un problème lors de la récupération des acomptes:', error);
        }
    });

</script>

<style scoped>
    .container {
        max-width: 600px;
    }

</style>
