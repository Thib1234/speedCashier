<template>
	<h1>liste des clients</h1>
	<template v-for="(product) in products" :key="product.id">
		<p v-text="product.name"/>
	</template>

</template>

<script setup>
import {
    ref
} from "vue"

const products = ref([]);

const loadFromServer = () => {
    axios.get('/api/products')
        .then((res) => products.value = res.data.data)
        .catch((e) => console.log(e))
}

loadFromServer();


</script>