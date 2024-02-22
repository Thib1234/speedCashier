<template>
    <div class="flex flex-col items-center">
        <h1 class="text-3xl font-bold mb-2">Caisse</h1>
        <div class="w-full max-w-8xl">
            <div class="flex items-center justify-between bg-gray-100 p-6 mb-6 rounded-lg">
                <input v-model="searchQuery" type="text" placeholder="Rechercher un produit"
                    class="w-full mr-4 py-3 px-6 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg">
                <button @click="searchQuery = ''"
                    class="py-3 px-6 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none text-lg">Effacer</button>
            </div>
            <div v-if="filteredProducts.length === 0" class="text-gray-500 text-center mb-8">Aucun produit trouvé.</div>
            <div v-else class="grid grid-cols-6 gap-6">
                <div v-for="product in filteredProducts" :key="product.id" @click="addToCart(product), searchQuery = ''"
                    class="bg-white p-6 rounded-lg shadow-md cursor-pointer hover:shadow-lg transition duration-300 ease-in-out">
                    <h2 class="text-xl font-bold mb-2">{{ product.name }}</h2>
                    <p class="text-gray-700 text-lg">{{ product.price }} €</p>
                    <p class="text-gray-700 text-xs">stock : {{ product.stock }}</p>
                </div>
            </div>
        </div>
        <div class="w-full max-w-3xl">
            <h2 class="text-2xl font-bold mb-4">Panier</h2>
            <div v-if="cart.length === 0" class="text-gray-500 text-center mb-4">Le panier est vide.</div>
            <div v-else>
                <!-- Ajoutez un champ pour saisir payment_id dans votre interface utilisateur -->


                <div v-for="(item, index) in cart" :key="index"
                    class="flex justify-between items-center bg-gray-100 p-6 mb-4 rounded-lg">
                    <div class="text-lg">{{ item.product.name }}</div>
                    <div class="text-lg">{{ item.product.price }} €</div>
                    <div class="text-xs">stock : {{ item.product.stock }}</div>

                    <div class="flex items-center">
                        <button @click="removeFromCart(index)"
                            class="text-red-500 font-bold focus:outline-none text-lg">-</button>
                        <div class="text-lg mx-3">{{ item.quantity }}</div>
                        <button @click="addToCart(item.product)"
                            class="text-green-500 font-bold focus:outline-none text-lg">+</button>
                    </div>
                </div>
                <div class="text-2xl font-bold text-right">Total: {{ totalAmount }} €</div>
                <input type="hidden" v-model.number="payment_id" name="payment_id">
                <button v-if="changeDue === 0" @click="sendSaleData"
                    class="w-full bg-blue-500 text-white py-4 rounded-lg mt-8 hover:bg-blue-600 focus:outline-none text-xl">Payer</button>
                <button @click="resetCart"
                    class="w-full bg-red-500 text-white py-4 rounded-lg mt-8 hover:bg-red-600 focus:outline-none text-xl">Annuler
                    la vente</button>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center">
        <h2 class="text-2xl font-bold mb-4">Moyens de paiement</h2>
        <div class="flex justify-between mb-4">
            <!-- Paiement en espèces (cash) -->
            <button @click="selectpaymentMethod('cash')"
                :class="{ 'bg-green-500': paymentMethod === 'cash', 'bg-gray-400': paymentMethod !== 'cash' }"
                class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-green-600 focus:outline-none">
                <span class="mr-2">Cash</span>
                <BanknotesIcon class="h-6 w-6" />
            </button>
            <!-- Paiement Bancontact -->
            <button @click="selectpaymentMethod('bancontact')"
                :class="{ 'bg-blue-500': paymentMethod === 'bancontact', 'bg-gray-400': paymentMethod !== 'bancontact' }"
                class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-blue-600 focus:outline-none">
                <span class="mr-2">Bancontact</span>
                <CreditCardIcon class="h-6 w-6" />
            </button>
            <!-- Paiement par carte de crédit -->
            <button @click="selectpaymentMethod('credit_card')"
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
        <button @click="addtotal">{{ Math.abs(changeDue) }}</button>
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
    const payment_id = ref(null);
    const products = ref({})
    const searchQuery = ref('');
    const cart = ref([]);
    const paymentMethod = ref('cash'); // Déclaration de la propriété paymentMethods
    const amountPaidCash = ref(0);
    const amountPaidBancontact = ref(0);
    const amountPaidCreditcard = ref(0);
    const loadFromServer = async () => {
        await axios.get('/api/products')
            .then((res) => products.value = res.data.data)
            .catch((e) => console.log(e))
    }
    loadFromServer();


    const addtotal = () => {
        amountPaidCash.value = Math.abs(changeDue.value)
    }
    const totalAmount = computed(() => {
        return cart.value.reduce((total, item) => total + (item.product.price * item.quantity), 0);
    });

    const changeDue = computed(() => {
        return (amountPaidCash.value + amountPaidBancontact.value + amountPaidCreditcard.value) - totalAmount.value;
    });
    const cartMultiplication = computed((a, b) => {
        return a * b;
    })


    const filteredProducts = ref([]);

    const selectpaymentMethod = (method) => {
        paymentMethod.value = method;
        // Mettre à jour paymentId en fonction du mode de paiement sélectionné
        if (method === 'cash') {
            payment_id.value = 1; // Remplacez par l'ID correspondant dans votre base de données
        } else if (method === 'bancontact') {
            payment_id.value = 2; // Remplacez par l'ID correspondant dans votre base de données
        } else if (method === 'credit_card') {
            payment_id.value = 3; // Remplacez par l'ID correspondant dans votre base de données
        }
    };

    const updateFilteredProducts = () => {
        const filtered = Object.values(products.value).filter(product =>
            product.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
        filteredProducts.value = filtered;
    };

    // const addToCart = (product) => {
    //     const existingItem = cart.value.find(item => item.product.id === product.id);
    //     if (existingItem) {
    //         existingItem.quantity++;
    //     } else {
    //         cart.value.push({
    //             product,
    //             quantity: 1
    //         });
    //     };
    // };

    // const addToCart = (product) => {
    //     const existingItem = cart.value.find(item => item.product.id === product.id);
    //     if (existingItem) {
    //         existingItem.quantity++;
    //     } else {
    //         console.log(product.value)
    //         cart.value.push({
    //             product: {
    //                 ...product,
    //                 price: product.price
    //             }, // Inclure le prix du produit dans l'objet du panier
    //             quantity: 1
    //         });
    //     };
    // };
    
//     const addToCart = (product) => {
//     const existingItem = cart.value.find(item => item.product.id === product.id);
//     if (existingItem) {
//         existingItem.quantity++;
//     } else {
//         if (typeof product.price !== 'undefined') {
//             console.log(typeof product)
//             cart.value.push({
//                 product: { ...product }, // Inclure le produit tel quel dans le panier
//                 quantity: 1
//             });
//         } else {
//             console.log(product)
//             console.error('Le prix du produit est indéfini.');
//             // Vous pouvez gérer cette erreur de manière appropriée, par exemple, en affichant un message à l'utilisateur.
//         }
//     }
// }
const addToCart = (product) => {
    const existingItem = cart.value.find(item => item.product.id === product.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        if (typeof product.price !== 'undefined') {
            console.log(product.price);
            cart.value.push({
                product: { ...product, price: product.price }, // Inclure le prix du produit dans l'objet du panier
                quantity: 1
            });
        } else {
            console.error('Le prix du produit est indéfini.');
            // Vous pouvez gérer cette erreur de manière appropriée, par exemple, en affichant un message à l'utilisateur.
        }
    }
}



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
        resetCart();
    };


    const sendSaleData = () => {
    // Construire l'objet de données à envoyer au contrôleur
    const requestData = {
        products: cart.value.map(item => ({
            id: item.product.id,
            quantity: item.quantity,
            price: item.product.price // Ajoutez le prix de chaque produit
        })),
        client_id: null, // Insérez l'ID du client si nécessaire
        payment_method: paymentMethod.value, // Récupérer la méthode de paiement sélectionnée
        total_amount: totalAmount.value, // Récupérer le montant total du panier
        payment_id: payment_id.value, // Ajoutez paymentId aux données envoyées
    };

    // Envoyer la requête POST au contrôleur Laravel
    axios.post('/api/sales', requestData)
        .then(response => {
            console.log(response.data); // Afficher la réponse du serveur (facultatif)
            // Réinitialiser le panier après avoir enregistré la vente
            resetCart();
        })
        .catch(error => {
            console.error('Erreur lors de l\'enregistrement de la vente:', error);
        });
};



    watchEffect(() => {
        updateFilteredProducts();
    });

    const resetCart = () => {
        cart.value = []
        totalAmount.value = 0
        changeDue.value = 0
        amountPaidCash.value = 0
        amountPaidBancontact.value = 0
        amountPaidCreditcard.value = 0
    }

</script>

<style>
  /* Styles Tailwind ici */
</style>