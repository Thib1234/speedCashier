<template>
	<h1>liste des clients</h1>
	<template v-for="(user) in users" :key="user.id">
		<p v-text="user.name"/>
	</template>

</template>

<script setup>
import {ref} from "vue"

	const users = ref([]);

	const loadFromServer = () => {
		axios.get('/api/users')
			.then((res) => users.value = res.data.data)
			.catch((e) => console.log(e))
	}

	loadFromServer();
</script>