<template>
    <div class="flex flex-col items-center">
        <h1 class="text-3xl font-bold mb-6">Caisse</h1>
        <div class="w-full max-w-3xl">
            <div class="flex items-center justify-between bg-gray-100 p-6 mb-6 rounded-lg">
                <input v-model="searchQuery" type="text" placeholder="Rechercher un produit"
                    class="w-full mr-4 py-3 px-6 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg">
                <button @click="searchQuery = ''"
                    class="py-3 px-6 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none text-lg">Effacer</button>
            </div>
            <div v-if="filteredProducts.length === 0" class="text-gray-500 text-center mb-8">Aucun produit trouvé.</div>
            <div v-else class="grid grid-cols-2 gap-6">
                <div v-for="product in filteredProducts" :key="product.id" @click="addToCart(product)"
                    class="bg-white p-6 rounded-lg shadow-md cursor-pointer hover:shadow-lg transition duration-300 ease-in-out">
                    <h2 class="text-xl font-bold mb-2">{{ product.name }}</h2>
                    <p class="text-gray-700 text-lg">{{ product.price }} €</p>
                </div>
            </div>
        </div>
        <div class="w-full max-w-3xl">
            <h2 class="text-2xl font-bold mb-4">Panier</h2>
            <div v-if="cart.length === 0" class="text-gray-500 text-center mb-4">Le panier est vide.</div>
            <div v-else>
                <div v-for="(item, index) in cart" :key="index"
                    class="flex justify-between items-center bg-gray-100 p-6 mb-4 rounded-lg">
                    <div class="text-lg">{{ item.product.name }}</div>
                    <div class="text-lg">{{ item.product.price }} €</div>
                    <div class="flex items-center">
                        <button @click="removeFromCart(index)"
                            class="text-red-500 font-bold focus:outline-none text-lg">-</button>
                        <div class="text-lg mx-3">{{ item.quantity }}</div>
                        <button @click="addToCart(item.product)"
                            class="text-green-500 font-bold focus:outline-none text-lg">+</button>
                    </div>
                </div>
                <div class="text-2xl font-bold text-right">Total: {{ totalAmount }} €</div>                
                <button @click="checkout"
                    class="w-full bg-blue-500 text-white py-4 rounded-lg mt-8 hover:bg-blue-600 focus:outline-none text-xl">Payer</button>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center">
        <h2 class="text-2xl font-bold mb-4">Moyens de paiement</h2>
        <div class="flex justify-between mb-4">
            <!-- Paiement en espèces (cash) -->
            <button @click="selectPaymentMethod('cash')"
                :class="{ 'bg-green-500': paymentMethod === 'cash', 'bg-gray-400': paymentMethod !== 'cash' }"
                class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-green-600 focus:outline-none">
                <span class="mr-2">Cash</span>
                <BanknotesIcon class="h-6 w-6" />
            </button>
            <!-- Paiement Bancontact -->
            <button @click="selectPaymentMethod('bancontact')"
                :class="{ 'bg-blue-500': paymentMethod === 'bancontact', 'bg-gray-400': paymentMethod !== 'bancontact' }"
                class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-blue-600 focus:outline-none">
                <span class="mr-2">Bancontact</span>
                <CreditCardIcon class="h-6 w-6" />
            </button>
            <!-- Paiement par carte de crédit -->
            <button @click="selectPaymentMethod('credit_card')"
                :class="{ 'bg-purple-500': paymentMethod === 'credit_card', 'bg-gray-400': paymentMethod !== 'credit_card' }"
                class="flex items-center text-white py-3 px-6 rounded-lg hover:bg-purple-600 focus:outline-none">
                <span class="mr-2">Carte de crédit</span>
                <CreditCardIcon class="h-6 w-6" />
            </button>
        </div>
        <!-- Montant payé et montant à rendre pour le paiement en espèces -->
        <div v-if="paymentMethod === 'cash'" class="flex items-center mb-4">
            <label for="amountPaidCash" class="mr-2">Montant payé:</label>
            <input id="amountPaidCash" type="number" v-model.number="amountPaidCash"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg">
            
        </div>
        <div v-if="paymentMethod === 'bancontact'" class="flex items-center mb-4">
            <label for="amountPaidBancontact" class="mr-2">Montant payé:</label>
            <input id="amountPaidBancontact" type="number" v-model.number="amountPaidBancontact"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg">
            
        </div>
        <div v-if="paymentMethod === 'credit_card'" class="flex items-center mb-4">
            <label for="amountPaidCreditcard" class="mr-2">Montant payé:</label>
            <input id="amountPaidCreditcard" type="number" v-model.number="amountPaidCreditcard"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg">
            
        </div>
        <!-- Le reste de votre section des moyens de paiement -->
        <span v-if="changeDue > 0" class="ml-4 text-blue-500">Montant à rendre: {{ changeDue }} €</span>
        <span v-if="changeDue < 0" class="ml-4 text-red-500">Reste à payer: {{ Math.abs(changeDue) }} €</span>
        <span v-if="changeDue === 0" class="ml-4 text-green-500">Compte juste</span>
    </div>
</template>
<script setup>
    import {
        ref,
        computed,
        watchEffect
    } from 'vue';
    import {
        BanknotesIcon,
        CreditCardIcon,
    } from '@heroicons/vue/24/outline'

    const products = ref({})
    const searchQuery = ref('');
    const cart = ref([]);
    const paymentMethod = ref('cash'); // Déclaration de la propriété paymentMethod
    const amountPaid = ref(0);
    const amountPaidCash = ref(0);
    const amountPaidBancontact = ref(0);
    const amountPaidCreditcard = ref(0);

    const totalAmount = computed(() => {
        return cart.value.reduce((total, item) => total + (item.product.price * item.quantity), 0);
    });

    const changeDue = computed(() => {
        return (amountPaidCash.value + amountPaidBancontact.value + amountPaidCreditcard.value) - totalAmount.value;
    });

    const loadFromServer = async () => {
        await axios.get('/api/products')
            .then((res) => products.value = res.data.data)
            .catch((e) => console.log(e))
    }
    loadFromServer();

    const filteredProducts = ref([]);

    const updateFilteredProducts = () => {
        const filtered = Object.values(products.value).filter(product =>
            product.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
        filteredProducts.value = filtered;
    };

    const addToCart = (product) => {
        const existingItem = cart.value.find(item => item.product.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.value.push({
                product,
                quantity: 1
            });
        };
    };

    const removeFromCart = (index) => {
        const item = cart.value[index];
        if (item.quantity > 1) {
            item.quantity--;
        } else {
            cart.value.splice(index, 1);
        }
    };

    const checkout = () => {
        // Logique de paiement
        console.log('Paiement effectué');
        // Réinitialiser le panier après le paiement
        cart.value = [];
    };

    const selectPaymentMethod = (method) => {
        paymentMethod.value = method;
    };

    // LOGIQUE DE PAIEMENT
    const processPayment = () => {
        if (paymentMethod.value === 'cash') {
            if (amountPaid.value < totalAmount.value) {
                alert('Le montant payé est insuffisant.');
            } else if (amountPaid.value > totalAmount.value) {
                alert(`Montant à rendre: ${changeDue.value} €`);
            } else {
                alert('Paiement en espèces effectué.');
            }
        } else if (paymentMethod.value === 'bancontact') {
            alert('Paiement Bancontact effectué.');
        } else if (paymentMethod.value === 'credit_card') {
            alert('Paiement par carte de crédit effectué.');
        }
    };
    watchEffect(() => {
        updateFilteredProducts();
    });

</script>

<style>
  /* Styles Tailwind ici */
</style>