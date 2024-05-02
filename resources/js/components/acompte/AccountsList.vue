<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Acomptes en cours</h2>
        <ul class="bg-white rounded-lg border border-gray-200 w-full p-4">
            <li v-for="acompte in acomptes" :key="acompte.id"
                class="flex justify-between items-center p-3 hover:bg-gray-100">
                <span>Client ID: {{ acompte.client_id }}</span>
                <span>Montant: {{ acompte.montant }}€</span>
                <span>Status: {{ acompte.status }}</span>
            </li>
        </ul>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from 'vue';

    const acomptes = ref([]);

    onMounted(async () => {
        try {
            const response = await axios.get('/api/acomptes');
            acomptes.value = response.data;
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
