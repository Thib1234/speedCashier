<template>
    <form>

        <div class="space-y-12 p-20">
            <h1>Créer un client</h1>

            <div class="border-b border-gray-900/10 pb-12">

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom du client</label>
                        <div class="mt-2">
                            <input v-model="formData.name" type="text" name="name" id="first-name"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email du client</label>
                        <div class="mt-2">
                            <input v-model="formData.email" type="text" name="email" id="email"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <label for="company" class="block text-sm font-medium leading-6 text-gray-900">Société</label>
                        <div class="mt-2">
                            <input v-model="formData.company" type="text" name="company" id="company"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <label for="tva" class="block text-sm font-medium leading-6 text-gray-900">TVA du client</label>
                        <div class="mt-2">
                            <input v-model="formData.tva" type="text" name="tva" id="tva"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <label for="adresse" class="block text-sm font-medium leading-6 text-gray-900">Adresse de la société</label>
                        <div class="mt-2">
                            <input v-model="formData.adresse" type="text" name="adresse" id="adresse"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <label for="code_postal" class="block text-sm font-medium leading-6 text-gray-900">Code postal de la société</label>
                        <div class="mt-2">
                            <input v-model="formData.code_postal" type="number" name="code_postal" id="code_postal"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        <label for="ville" class="block text-sm font-medium leading-6 text-gray-900">Ville</label>
                        <div class="mt-2">
                            <input v-model="formData.ville" type="text" name="ville" id="ville"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>


                </div>
                <button type="button" v-on:click="createClient"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Créer un nouveau client
                </button>
            </div>
            <span v-if="success"
                class="w-full bg-green-500 text-white py-4 rounded-lg mt-8 focus:outline-none text-xl">Client ajouté avec succès</span>
        </div>

    </form>

</template>

<script setup>
import {
    ref
} from 'vue'
const errors = ref({});

const formData = ref({
    name: '',
    email: '',
    tva: '',
    company: '',
    adresse: '',
    code_postal: '',
    ville: '',
})
let success = ref(false);


const createClient = async () => {
    await axios.post('/api/clients', formData.value)
        .then((res) => {
            errors.value = {};
            formData.value = {};
            success.value = true;
        })
        .catch((err) => errors.value = err.response.data.errors);
}


</script>
