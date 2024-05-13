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
                            @change="query = $event.target.value" />
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
            <input type="hidden" v-model.number="payment_id" name="payment_id" />
            <div class="flex flex-col items-center">
                <h2 class="text-2xl font-bold mb-4">Moyens de paiement</h2>
                <div class="flex justify-between mb-4">
                    <!-- Paiement en espèces (cash) -->
                    <button type="button" @click="selectpaymentMethod('cash')" :class="{
                    'bg-green-500': paymentMethod === 'cash',
                    'bg-gray-400': paymentMethod !== 'cash',
                }" class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-green-600 focus:outline-none">
                        <span class="mr-2">Cash</span>
                        <BanknotesIcon class="h-6 w-6" />
                    </button>
                    <button type="button" @click="selectpaymentMethod('bancontact')" :class="{
                    'bg-blue-500': paymentMethod === 'bancontact',
                    'bg-gray-400': paymentMethod !== 'bancontact',
                }" class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-blue-600 focus:outline-none">
                        <span class="mr-2">Bancontact</span>
                        <CreditCardIcon class="h-6 w-6" />
                    </button>
                    <button type="button" @click="selectpaymentMethod('credit_card')" :class="{
                    'bg-purple-500': paymentMethod === 'credit_card',
                    'bg-gray-400': paymentMethod !== 'credit_card',
                }" class="flex items-center text-white py-3 px-6 rounded-lg hover:bg-purple-600 focus:outline-none">
                        <span class="mr-2">Carte de crédit</span>
                        <CreditCardIcon class="h-6 w-6" />
                    </button>
                    <button type="button" @click="selectpaymentMethod('virement')" :class="{
                    'bg-red-300': paymentMethod === 'virement',
                    'bg-gray-400': paymentMethod !== 'virement',
                }" class="flex items-center text-white py-3 px-6 ml-3 rounded-lg hover:bg-red-400 focus:outline-none">
                        <span class="mr-2">Virement</span>
                        <CreditCardIcon class="h-6 w-6" />
                    </button>
                </div>
                <div v-if="paymentMethod === 'cash'" class="flex items-center mb-4">
                    <label for="amountPaidCash" class="mr-2">Montant payé:</label>
                    <input id="amountPaidCash" type="number" v-model.number="amountPaidCash"
                        class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
                </div>
                <div v-if="paymentMethod === 'bancontact'" class="flex items-center mb-4">
                    <label for="amountPaidBancontact" class="mr-2">Montant payé:</label>
                    <input inputmode="numeric" id="amountPaidBancontact" type="number"
                        v-model.number="amountPaidBancontact"
                        class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
                </div>
                <div v-if="paymentMethod === 'credit_card'" class="flex items-center mb-4">
                    <label for="amountPaidCreditcard" class="mr-2">Montant payé:</label>
                    <input id="amountPaidCreditcard" type="number" v-model.number="amountPaidCreditcard"
                        class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
                </div>
                <div v-if="paymentMethod === 'virement'" class="flex items-center mb-4">
                    <label for="amountPaidVirement" class="mr-2">Montant payé:</label>
                    <input id="amountPaidVirement" type="number" v-model.number="amountPaidVirement"
                        class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button
                    class="w-full bg-blue-500 text-white py-4 rounded-lg mt-8 hover:bg-blue-600 focus:outline-none text-xl">
                    Payer
                </button>
            </div>

        </form>
        <div v-if="success" class="fixed top-5 right-5 bg-green-200 text-green-800 text-sm px-4 py-3 rounded">
            Accompte créé avec succès
        </div>
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
        ChevronUpDownIcon,
        BanknotesIcon,
        CreditCardIcon
    } from '@heroicons/vue/20/solid';

    const people = ref([]);
    const clients = ref([]);
    const acompte = ref({
        client_id: '',
        montant: '',
        person_id: '',
    });
    const paymentMethod = ref("cash");
    const amountPaidCash = ref(0);
    const amountPaidBancontact = ref(0);
    const amountPaidCreditcard = ref(0);
    const amountPaidVirement = ref(0);
    const payment_id = ref(null);
    const success = ref(false);

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
            const montant = amountPaidCash.value + amountPaidBancontact.value + amountPaidCreditcard.value +
                amountPaidVirement.value;
            const payload = {
                client_id: acompte.value.client_id,
                montant: montant,
                product_id: acompte.value.person_id,
                cash: amountPaidCash.value,
                bancontact: amountPaidBancontact.value,
                credit_card: amountPaidCreditcard.value,
                virement: amountPaidVirement.value,
            };
            if (acompte.value.product_id) {
                payload.product_id = acompte.value.product_id;
            }
            const response = await axios.post('/api/acompte/store', payload);
            console.log(response.data);
            acompte.value.client_id = null;
            selected.value = null;
            success.value = true;
            setTimeout(() => {
                success.value = false;
            }, 3000);
        } catch (error) {
            if (error.response) {
                console.log(error.response.data);
                alert('Erreur lors de la création de l\'acompte: ' + error.response.data.message);
            } else {
                console.log('Error', error.message);
            }
        }
    }

    const selectpaymentMethod = (method) => {
        paymentMethod.value = method;
        if (method === "cash") {
            payment_id.value = 1;
        } else if (method === "bancontact") {
            payment_id.value = 2;
        } else if (method === "credit_card") {
            payment_id.value = 3;
        }
    };

</script>
