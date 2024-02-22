<template>
    <div>
        <h1 class="text-3xl font-bold mb-4">Liste des clients</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="client in clients" :key="client.id" class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-2">{{ client.name }}</h2>
                <!-- Ajoutez d'autres informations sur le client si nécessaire -->
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref
} from 'vue';

const clients = ref({})

const loadFromServer = async() => {
    await axios.get('/api/clients')
        .then((res) => clients.value = res.data.data)
        .catch((e) => console.log(e))
}

loadFromServer();
</script>