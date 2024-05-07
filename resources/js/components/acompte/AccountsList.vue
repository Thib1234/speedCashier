<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Acomptes en cours</h2>
        <ul class="bg-white rounded-lg border border-gray-200 w-full p-4 divide-y divide-gray-200">
            <li v-for="acompte in acomptes" :key="acompte.id" @click="showModal(acompte)"
                class="flex justify-between items-center p-3 hover:bg-gray-100 cursor-pointer">
                <span>Client: {{ acompte.client.name }}</span>
                <span>Montant: {{ acompte.montant }}€</span>
                <span>Status: {{ acompte.status }}</span>
                <span>Article: {{acompte.product.name}}</span>
                <span>Montant: {{acompte.product.price}} €</span>
                <span>Déjà payé: {{acompte.montant}} €</span>
                <span>Reste à payer: {{ acompte.rest }} €</span>
            </li>
        </ul>
    </div>
    <div v-if="modalVisible"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg p-5">
            <div class="flex justify-between items-center">
                <h3 class="text-lg">Valider ou annuler l'acompte</h3>
                <button class="text-gray-600" @click="modalVisible = false">&times;</button>
            </div>
            <p class="my-4">Changer le statut de l'acompte pour : {{ selectedAcompte.product.name }}</p>
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
                <button v-if="changeDue === 0"
                    class="w-full bg-blue-500 text-white py-4 rounded-lg mt-8 hover:bg-blue-600 focus:outline-none text-xl">
                    Payé
                </button>
                <span v-if="changeDue > 0" class="ml-4 text-blue-500">Montant à rendre: {{ changeDue }} €</span>
                <span v-if="changeDue < 0" class="ml-4 text-red-500">Reste à payer: {{ Math.abs(changeDue) }} €</span>
                <span v-if="changeDue === 0" class="ml-4 text-green-500">Compte juste</span>
            </div>
            <div class="flex items-center justify-between mt-5">
                <button type="button" @click="cancel"
                    class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Remboursé
                </button>
            </div>
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
    const paymentMethod = ref("cash");
    const amountPaidCash = ref(0);
    const amountPaidBancontact = ref(0);
    const amountPaidCreditcard = ref(0);
    const amountPaidVirement = ref(0);
    const payment_id = ref(null);
    const showModal = (acompte) => {
        selectedAcompte.value = acompte;
        modalVisible.value = true;
    };

    const validateAcompte = async () => {
        try {
            const response = await axios.post(`/api/account/${selectedAcompte.value.id}/apply-acompte`, {
                status: 'validé'
            });
            selectedAcompte.value.status = 'validé';
            console.log('Statut mis à jour:', response.data);

            const saleData = {
                client_id: selectedAcompte.value.client_id,
                total_amount: selectedAcompte.value.montant,
            };
            const saleResponse = await axios.post('/api/acompte/store',
                saleData);
            console.log('Vente créée avec succès:', saleResponse.data);

            modalVisible.value = false;
        } catch (error) {
            console.error('Erreur lors de la mise à jour du statut ou de la création de la vente:', error);
        }
    };
    const cancel = async () => {
        const response = await axios.post(`/api/account/${selectedAcompte.value.id}/cancel`, {
            status: 'remboursé'
        });
        selectedAcompte.value.status = 'remboursé';
        console.log('Statut mis à jour : ', response.data);

        modalVisible.value = false;
        loadAccounts();
    }
    const loadAccounts = async () => {
        try {
            const response = await axios.get('/api/acomptes');
            acomptes.value = response.data;
            console.log(response);
        } catch (error) {
            console.error('Il y a eu un problème lors de la récupération des acomptes:', error);
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
    const changeDue = computed(() => {
        return (
            amountPaidCash.value +
            amountPaidBancontact.value +
            amountPaidCreditcard.value +
            amountPaidVirement.value -
            totalAmount.value
        );
    });
    onMounted(async () => {
        loadAccounts();
    });

</script>
