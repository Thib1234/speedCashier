<template>
    <div class="flex flex-col items-center">
        <div class="container mx-auto p-6">
            <h1 class="text-4xl font-bold mb-6 text-center text-gray-800">Caisse</h1>
            <div class="flex items-center justify-between bg-gray-100 p-6 mb-6 rounded-lg">
                <input v-model="searchQuery" type="text" placeholder="Rechercher un produit"
                    class="w-full mr-4 py-3 px-6 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
                <button @click="searchQuery = ''"
                    class="py-3 px-6 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none text-lg transition ease-in-out duration-300">
                    Effacer
                </button>
            </div>
            <div class="grid grid-cols-6 justify-between w-full mx-1">
                <button v-for="category in categories" :key="category.id" @click="filterByCategory(category.id)" :class="{
            'bg-gray-300 hover:bg-gray-400': currentCategoryId !== category.id,
            'bg-red-400 hover:bg-gray-500': currentCategoryId === category.id
        }" class="text-gray-800 font-bold py-2 px-4 rounded m-1">
                    {{ category.name }}
                </button>
            </div>
            <div v-if="filteredProducts.length === 0" class="empty-state text-gray-500 text-center">
                Aucun produit trouvé.
            </div>
            <div v-else class="products-grid grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div v-for="product in displayedProducts" :key="product.id"
                    @click="addToCart(product), (searchQuery = '')"
                    class="product-card bg-white p-6 rounded-lg shadow transition duration-300 hover:shadow-lg">
                    <h2 class="product-name text-2xl font-bold mb-2">{{ product.name }}</h2>
                    <p class="product-price text-lg text-gray-700">{{ product.price }} €</p>
                </div>
            </div>
            <div v-if="displayedProducts.length < filteredProducts.length" class="load-more mt-6 text-center">
                <button @click="showMore"
                    class="load-more-button bg-blue-500 text-white font-bold py-3 px-6 rounded hover:bg-blue-600 focus:outline-none transition duration-300">
                    Afficher plus
                </button>
            </div>
        </div>
        <div class="w-full max-w-3xl mt-10">
            <div class="flex flex-col items-center">
                <div>
                    <div class="flex">
                        <button @click="showPopUpTempProduct = true"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-4">
                            Ajouter Produit Temporaire
                        </button>
                        <button v-if="client_id == null" @click="showPopup = true"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Sélectionner un client
                        </button>
                        <div v-else>
                            <span class="mr-2">Client selectionné :
                                {{ clients[client_id - 1].name }}</span>
                            <button @click="showPopup = true"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Changer de client
                            </button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-5"
                                @click="client_id = null">
                                Ne plus selectionner de client
                            </button>
                        </div>
                    </div>
                    <div v-if="showPopup"
                        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
                        <div class="bg-white p-8 max-w-md mx-auto rounded shadow-lg">
                            <h2 class="text-lg font-bold mb-4">
                                Sélectionner un client
                            </h2>
                            <div class="w-full max-w-8xl">
                                <div class="flex items-center justify-between bg-gray-100 p-6 mb-6 rounded-lg">
                                    <input v-model="searchQueryClient" type="text" placeholder="Rechercher un client"
                                        class="w-full mr-4 py-3 px-6 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
                                    <button @click="searchQueryClient = ''"
                                        class="py-3 px-6 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none text-lg">
                                        Effacer
                                    </button>
                                </div>
                                <div v-if="filteredClients.length === 0" class="text-gray-500 text-center mb-8">
                                    Aucun client trouvé.
                                </div>
                                <div v-else class="grid grid-cols-2 gap-6">
                                    <div v-for="client in filteredClients" :key="client.id" @click="
                                            addClient(client),
                                                (showPopup = false)
                                        "
                                        class="bg-white p-6 rounded-lg shadow-md cursor-pointer hover:shadow-lg transition duration-300 ease-in-out">
                                        <h2 class="text-xl font-bold mb-2">
                                            {{ client.name }}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <button @click="showPopup = false"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mt-10">
                                Fermer
                            </button>
                        </div>
                    </div>
                    <div v-if="showPopUpTempProduct"
                        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
                        <div class="bg-white p-8 max-w-md mx-auto rounded shadow-lg">
                            <h2 class="text-lg font-bold mb-4">
                                Ajouter un produit temporaire
                            </h2>
                            <div class="w-full max-w-8xl">
                                <div v-for="(
                                        product, index
                                    ) in temporaryProducts" :key="index">
                                    <p>
                                        {{ product.name }} -
                                        {{ product.price }} €
                                    </p>
                                </div>
                                <label for="product-name" class="block font-semibold mb-2">Nom du produit :</label>
                                <input v-model="tempProductName" id="product-name" type="text"
                                    class="w-full px-4 py-2 border rounded mb-4" />
                                <label for="product-price" class="block font-semibold mb-2">Prix du produit :</label>
                                <input v-model="tempProductPrice" id="product-price" type="number" step="0.01"
                                    class="w-full px-4 py-2 border rounded mb-4" />
                                <button @click="
                                        addTemporaryProduct(
                                            tempProductName,
                                            tempProductPrice
                                        ),
                                            (showPopUpTempProduct = false)
                                    "
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mt-4">
                                    Ajouter
                                </button>
                                <button @click="showPopUpTempProduct = false"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mt-4">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="text-2xl font-bold mb-4 text-center">Panier</h2>
            <div v-if="cart.length === 0" class="text-gray-500 text-center mb-4">
                Le panier est vide.
            </div>
            <div v-else>
                <div v-for="(item, index) in cart" :key="index"
                    class="flex justify-between items-center bg-gray-100 p-6 mb-4 rounded-lg">
                    <div class="text-lg">{{ item.product.name }}</div>
                    <div class="text-lg">{{ item.product.price }} €</div>
                    <div class="flex items-center">
                        <button @click="removeFromCart(index)"
                            class="text-red-500 font-bold focus:outline-none text-lg">
                            -
                        </button>
                        <div class="text-lg mx-3">{{ item.quantity }}</div>
                        <button @click="addToCart(item.product)"
                            class="text-green-500 font-bold focus:outline-none text-lg">
                            +
                        </button>
                    </div>
                </div>
                <div class="text-2xl font-bold text-right">
                    Total: {{ totalAmount }} €
                </div>
                <input type="hidden" v-model.number="payment_id" name="payment_id" />
                <div>
                    <button v-if="changeDue === 0" @click="confirmPayment"
                        class="w-full bg-blue-500 text-white py-4 rounded-lg mt-8 hover:bg-blue-600 focus:outline-none text-xl">
                        Payer
                    </button>
                </div>
                <div v-if="showConfirmationModal"
                    class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
                    <div class="bg-white p-8 max-w-md mx-auto rounded shadow-lg">
                        <h2 class="text-lg font-bold mb-4">
                            Confirmation de paiement
                        </h2>
                        <p class="mb-4">
                            Voulez-vous créer un ticket de caisse pour cette
                            transaction ?
                        </p>
                        <div class="flex justify-between">
                            <button @click="createTicketAndPay"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Oui
                            </button>
                            <button @click="cancelPayment"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Non
                            </button>
                        </div>
                    </div>
                </div>
                <button @click="resetCart"
                    class="w-full bg-red-500 text-white py-4 rounded-lg mt-8 hover:bg-red-600 focus:outline-none text-xl">
                    Annuler la vente
                </button>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center">
        <h2 class="text-2xl font-bold mb-4">Moyens de paiement</h2>
        <div class="flex justify-between mb-4">
            <!-- Paiement en espèces (cash) -->
            <button @click="selectpaymentMethod('cash')" :class="{
                    'bg-green-500': paymentMethod === 'cash',
                    'bg-gray-400': paymentMethod !== 'cash',
                }"
                class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-green-600 focus:outline-none">
                <span class="mr-2">Cash</span>
                <BanknotesIcon class="h-6 w-6" />
            </button>
            <button @click="selectpaymentMethod('bancontact')" :class="{
                    'bg-blue-500': paymentMethod === 'bancontact',
                    'bg-gray-400': paymentMethod !== 'bancontact',
                }" class="flex items-center text-white py-3 px-6 rounded-lg mr-4 hover:bg-blue-600 focus:outline-none">
                <span class="mr-2">Bancontact</span>
                <CreditCardIcon class="h-6 w-6" />
            </button>
            <button @click="selectpaymentMethod('credit_card')" :class="{
                    'bg-purple-500': paymentMethod === 'credit_card',
                    'bg-gray-400': paymentMethod !== 'credit_card',
                }" class="flex items-center text-white py-3 px-6 rounded-lg hover:bg-purple-600 focus:outline-none">
                <span class="mr-2">Carte de crédit</span>
                <CreditCardIcon class="h-6 w-6" />
            </button>
            <button @click="selectpaymentMethod('virement')" :class="{
                    'bg-red-300': paymentMethod === 'virement',
                    'bg-gray-400': paymentMethod !== 'virement',
                }" class="flex items-center text-white py-3 px-6 ml-3 rounded-lg hover:bg-red-400 focus:outline-none">
                <span class="mr-2">Virement</span>
                <CreditCardIcon class="h-6 w-6" />
            </button>
        </div>
        <div v-if="paymentMethod === 'cash'" class="flex items-center mb-4">
            <label for="amountPaidCash" class="mr-2">Montant payé:</label>
            <input inputmode="numeric" id="amountPaidCash" type="number" v-model.number="amountPaidCash"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
        </div>
        <div v-if="paymentMethod === 'bancontact'" class="flex items-center mb-4">
            <label for="amountPaidBancontact" class="mr-2">Montant payé:</label>
            <input inputmode="numeric" id="amountPaidBancontact" type="number" v-model.number="amountPaidBancontact"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
        </div>
        <div v-if="paymentMethod === 'credit_card'" class="flex items-center mb-4">
            <label for="amountPaidCreditcard" class="mr-2">Montant payé:</label>
            <input inputmode="numeric" id="amountPaidCreditcard" type="number" v-model.number="amountPaidCreditcard"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
        </div>
        <div v-if="paymentMethod === 'virement'" class="flex items-center mb-4">
            <label for="amountPaidVirement" class="mr-2">Montant payé:</label>
            <input inputmode="numeric" id="amountPaidVirement" type="number" v-model.number="amountPaidVirement"
                class="w-32 py-2 px-4 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500 text-lg" />
        </div>
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
        onMounted,
        watchEffect
    } from "vue";
    import {
        BanknotesIcon,
        CreditCardIcon
    } from "@heroicons/vue/24/outline";

    const payment_id = ref(null);
    const products = ref([]);
    const categories = ref(null);
    const clients = ref({});
    const client_id = ref(null);
    const searchQuery = ref("");
    const searchQueryClient = ref("");
    const cart = ref([]);
    const paymentMethod = ref("cash");
    const amountPaidCash = ref(0);
    const amountPaidBancontact = ref(0);
    const amountPaidCreditcard = ref(0);
    const amountPaidVirement = ref(0);
    const showPopup = ref(false);
    const showPopUpTempProduct = ref(false);
    const filteredClients = ref([]);
    const sale_id = ref(null);
    const showConfirmationModal = ref(false);
    const temporaryProducts = ref([]);
    const tempProductName = ref(null);
    const tempProductPrice = ref(null);
    const currentCategoryId = ref(null);
    const maxDisplayedProducts = ref(15);

    const filteredProducts = computed(() => {
        if (currentCategoryId.value === null) {
            return products.value.filter(product =>
                product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            );
        } else {
            return products.value.filter(product =>
                product.category_id === currentCategoryId.value &&
                product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            );
        }
    });

    const displayedProducts = computed(() => {
        return filteredProducts.value.slice(0, maxDisplayedProducts.value);
    });

    const loadFromServer = async () => {
        try {
            const response = await axios.get('/api/productsShow');
            products.value = response.data.products.data;
            categories.value = response.data.categories.data;
            // console.log("Products and Categories loaded", products.value, categories.value);
        } catch (error) {
            console.error("Error loading data from server:", error);
        }
    };

    const addTemporaryProduct = (name, price) => {
        const tempProductId = "_" + Math.random().toString(36).substring(2, 9); // fonction en remplacement de substr()
        const tempProduct = {
            id: tempProductId,
            name,
            price
        };
        temporaryProducts.value.push(tempProduct);
        addToCart(tempProduct);
        tempProductName.value = null;
        tempProductPrice.value = null;
    };

    const addtotal = () => {
        if (paymentMethod.value === "cash") {
            amountPaidCash.value = Math.abs(changeDue.value);
        } else if (paymentMethod.value === "bancontact") {
            amountPaidBancontact.value = Math.abs(changeDue.value);
        } else if (paymentMethod.value === "credit_card") {
            amountPaidCreditcard.value = Math.abs(changeDue.value);
        } else if (paymentMethod.value === "virement") {
            amountPaidVirement.value = Math.abs(changeDue.value);
        }
    };

    const totalAmount = computed(() => {
        return cart.value.reduce((total, item) => total + item.product.price * item.quantity, 0);
    });

    const changeDue = computed(() => {
        return amountPaidCash.value + amountPaidBancontact.value + amountPaidCreditcard.value +
            amountPaidVirement.value - totalAmount.value;
    });

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

    const updateFilteredClients = () => {
        const filtered = Object.values(clients.value).filter(client =>
            client.name.toLowerCase().includes(searchQueryClient.value.toLowerCase())
        );
        filteredClients.value = filtered;
    };

    const addClient = (client) => {
        client_id.value = client.id;
    };

    const addToCart = (product) => {
        const existingItem = cart.value.find(item => item.product.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            if (typeof product.price !== "undefined") {
                cart.value.push({
                    product: {
                        ...product,
                        price: product.price
                    },
                    quantity: 1
                });
            } else {
                console.error("Le prix du produit est indéfini.");
            }
        }
    };

    const removeFromCart = (index) => {
        const item = cart.value[index];
        if (item.quantity > 1) {
            item.quantity--;
        } else {
            cart.value.splice(index, 1);
        }
    };

    const sendSaleData = async () => {
        const requestData = {
            products: cart.value.map(item => ({
                id: item.product.id,
                quantity: item.quantity,
                price: item.product.price,
                name: item.product.name,
            })),
            client_id: client_id.value,
            total_amount: totalAmount.value,
            cash: amountPaidCash.value,
            bancontact: amountPaidBancontact.value,
            credit_card: amountPaidCreditcard.value,
            virement: amountPaidVirement.value,
        };

        await axios.post("/api/sales", requestData)
            .then(response => {
                resetCart();
                loadFromServer();
                sale_id.value = response.data.sale_id;
                // console.log("ID de la vente:", sale_id.value);
                // generateTicket(sale_id.value);
            })
            .catch(error => {
                console.error("Erreur lors de l'enregistrement de la vente:", error);
            });
    };

    watchEffect(() => {
        updateFilteredClients();
    });

    const resetCart = () => {
        cart.value = [];
        amountPaidCash.value = 0;
        amountPaidBancontact.value = 0;
        amountPaidCreditcard.value = 0;
        amountPaidVirement.value = 0;
        client_id.value = null;
    };

    const showMore = () => {
        maxDisplayedProducts.value += 5;
    };

    const confirmPayment = () => {
        showConfirmationModal.value = true;
    };

    const cancelPayment = () => {
        sendSaleData();
        showConfirmationModal.value = false;
    };

    const createTicketAndPay = () => {
        sendSaleData();
        // generateTicket();
        showConfirmationModal.value = false;
    };

    // const generateTicket = (saleId) => {
    //     axios.post("/api/pdf", saleId)
    //         .then(response => {
    //             // console.log(response);
    //         })
    //         .catch(error => {
    //             console.error("Erreur lors de la génération du ticket de caisse:", error);
    //         });
    // };

    const filterByCategory = (categoryId) => {
        currentCategoryId.value = categoryId === currentCategoryId.value ? null : categoryId;
    };

    onMounted(() => {
        loadFromServer();
    });

</script>
