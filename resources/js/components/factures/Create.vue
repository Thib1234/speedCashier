<template>
    <div>
		<h1>
			Création de Facture
		</h1>
        <form @submit.prevent="createFacture">
            <div class="mb-4">
                <label for="sale">Selectionner une vente</label>
                <select name="sale" id="sale" v-model="formData.id">
                    <option disabled value="">Veuillez selectionner une vente</option>
                    <option v-for="sale in sales" :key="sale.id" :value="sale.id">{{ sale.total_amount }}</option>
                </select>
            </div>
			<div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 text-white ml-2 p-4 rounded-lg mt-8 hover:bg-blue-600 focus:outline-none text-xl">
                    Créer la facture
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
	const formData = ref({
        id: '',
    })
    const sales = ref(null);
    const loadFromServer = async () => {
        try {
            const response = await axios.get('/api/factures/create');
            sales.value = response.data;
            console.log(sales.value);
        } catch (error) {
            console.log(error);
        }
    }
	const createFacture = async () => {
		const response = await axios.post('/api/factures/store', formData.value);
		console.log(response);
	}

    onMounted(async () => {
        loadFromServer();
    });

</script>
