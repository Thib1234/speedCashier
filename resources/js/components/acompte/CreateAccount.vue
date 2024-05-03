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
            <label class="block text-gray-700 text-sm font-bold mb-2">Article</label>
            <Combobox v-model="selected" v-slot="{ open }">
                <div class="relative mt-1">
                    <div
                        class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                        <ComboboxInput
                            class="ntm w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                            :displayValue="(person) => person ? (person.name + ' ' + person.price + ' €' ) : ''"
                            @change="query = $event.target.value"/>
                        <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </ComboboxButton>
                    </div>
                    <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0"
                        @after-leave="query = ''">
                        <ComboboxOptions
                            class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                            static>
                            <div v-if="filteredPeople.length === 0 && query !== ''"
                                class="relative cursor-default select-none px-4 py-2 text-gray-700">
                                Aucun produit trouvé.
                            </div>

                            <ComboboxOption as="div" v-for="person in filteredPeople" :key="person.id" :value="person"
                                v-slot="{ selected, active }">
                                <li class="relative cursor-default select-none py-2 pl-10 pr-4" :class="{
                    'bg-teal-600 text-white': active,
                    'text-gray-900': !active,
                }">
                                    <span class="block truncate"
                                        :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                        {{ person.name }} ({{ person.price }} €)
                                    </span>
                                    <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3"
                                        :class="{ 'text-white': active, 'text-teal-600': !active }">
                                        <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                    </span>
                                </li>
                            </ComboboxOption>
                        </ComboboxOptions>
                    </TransitionRoot>
                </div>
            </Combobox>
            <div class="mb-6">
                <label for="montant" class="block text-gray-700 text-sm font-bold mb-2">Montant de l'accompte :</label>
                <input type="number" id="montant" v-model="acompte.montant"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>
            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Créer un acompte
                </button>
            </div>
        </form>
    </div>
</template>
<script setup>
    import {
        ref,
        watch,
        onMounted
    } from 'vue';
    import {
        Combobox,
        ComboboxInput,
        ComboboxButton,
        ComboboxOptions,
        ComboboxOption,
        TransitionRoot
    } from '@headlessui/vue';
    import {
        CheckIcon,
        ChevronUpDownIcon
    } from '@heroicons/vue/20/solid';

    const people = ref([]);
    const clients = ref([]);
    const acompte = ref({
        client_id: '',
        montant: '',
        person_id: '',
    });

    const selected = ref(null);
    const query = ref('');
    const filteredPeople = ref([]);

    watch(people, (newValue) => {
        filteredPeople.value = query.value === '' ? newValue : filterPeople(newValue, query.value);
    }, {
        immediate: true
    });

    watch(query, (newQuery) => {
        filteredPeople.value = newQuery === '' ? people.value : filterPeople(people.value, newQuery);
    });

    watch(selected, (newValue) => {
        if (newValue) {
            acompte.value.person_id = newValue.id;
        } else {
            acompte.value.person_id = '';
        }
    });

    function filterPeople(people, query) {
        return people.filter(person =>
            person.name.toLowerCase().replace(/\s+/g, '').includes(query.toLowerCase().replace(/\s+/g, ''))
        );
    }

    onMounted(async () => {
        try {
            const clientsResponse = await axios.get('/api/clients');
            clients.value = clientsResponse.data.data;
            const response = await axios.get('/api/productsWithoutToilettage');
            people.value = response.data.data;

        } catch (error) {
            console.error('Erreur lors de la récupération des données:', error);
        }
    });

    const submitAcompte = async () => {
        try {
            const payload = {
                client_id: acompte.value.client_id,
                montant: acompte.value.montant,
                product_id: acompte.value.person_id
            };
            if (acompte.value.product_id) {
                payload.product_id = acompte.value.product_id;
            }
            const response = await axios.post('/api/acompte/store', payload);
            console.log(response.data);
        } catch (error) {
            if (error.response) {
                console.log(error.response.data);
                alert('Erreur lors de la création de l\'acompte: ' + error.response.data.message);
            } else {
                console.log('Error', error.message);
            }
        }
    }

</script>
