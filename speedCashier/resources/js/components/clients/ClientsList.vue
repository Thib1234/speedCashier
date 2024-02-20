<template>
	<h1>liste des clients</h1>
	<template v-for="(client) in clients" :key="client.id">
		<p v-text="client.name"/>
	</template>

</template>

<script setup>

import { ref } from 'vue';

const clients = ref({})

const loadFromServer = () => {
    axios.get('/api/clients')
        .then((res) => clients.value = res.data.data)
        .catch((e) => console.log(e))
}

loadFromServer();
</script>