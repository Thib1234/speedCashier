<template>
    <div class="grid grid-cols-5 h-screen">
        <div class="col-span-1">
            <div class="h-16 flex items-center justify-center border-b">
                <h2 class="text-2xl font-semibold">Catégories</h2>
            </div>
            <div class="grid grid-cols-1 justify-between w-full mx-1">
                <button v-for="category in categories" :key="category.id" @click="filterByCategory(category.id)"
                    class="font-bold py-2 px-4 rounded m-1">
                    {{ category.name }}
                </button>
            </div>
        </div>
        <div class="col-span-3 mx-3">
            <div class="grid grid-cols-4 gap-4">
                <div v-for="product in displayedProducts" :key="product.id" @click="addToCart(product), (searchQuery = '')"
                    class="product-card bg-white p-2 rounded-lg shadow transition duration-300 hover:shadow-lg">
                    <h2 class="product-name text-2xl font-bold mb-2">{{ product.name }}</h2>
                    <p class="product-price text-lg text-gray-700">{{ product.price }} €</p>
                    <p v-if="product.stock > 0" class="product-stock text-sm text-gray-700">
                        Stock : {{ product.stock }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-span-1 mr-2">
            <div class="h-16 flex items-center justify-center border-b">
                <h2 class="font-semibold">Caisse</h2>
            </div>
            <div v-if="cart.length === 0" class="text-gray-500 text-center mb-4">
                Le panier est vide.
            </div>
            <div v-else>
                <div v-for="(item, index) in cart" :key="index"
                    class="flex justify-between items-center bg-gray-100 p-2 mb-4 rounded-lg ">
                    <div>
                        <div class="text-lg">{{ item.product.name }}</div>
                        <div class="text-lg">{{ item.product.price }} €</div>
                    </div>

                    <div v-if="item.product.stock >= 0 && item.product.stock" class="text-xs">
                        stock : {{ item.product.stock }}
                    </div>
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
</template>
<script setup>
    import {
        ref,
        computed,
        watchEffect,
        onMounted,
    } from "vue";
    import {
        BanknotesIcon,
        CreditCardIcon
    } from "@heroicons/vue/24/outline";
    onMounted(() => {
        loadFromServer();
    });
    const payment_id = ref(null);
    const products = ref({});
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
    const filteredProducts = ref([]);
    const filteredClients = ref([]);
    const sale_id = ref(null);
    const showConfirmationModal = ref(false);
    const temporaryProducts = ref([]);
    const tempProductName = ref(null);
    const tempProductPrice = ref(null);
    const currentCategoryId = ref(null);
    const maxDisplayedProducts = ref(12);
    const displayedProducts = ref([]);

    const updateDisplayedProducts = () => {
        displayedProducts.value = filteredProducts.value.slice(
            0,
            maxDisplayedProducts.value
        );
    };
    const updateFilteredProducts = () => {
        const filtered = filteredProducts.value.filter((product) =>
            product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
        filteredProducts.value = filtered;
        updateDisplayedProducts();
    };

    const addTemporaryProduct = (name, price) => {
        // Générer un ID temporaire unique pour chaque produit temporaire
        const tempProductId = "_" + Math.random().toString(36).substr(2, 9);

        const tempProduct = {
            id: tempProductId,
            name: name,
            price: price,
        };
        temporaryProducts.value.push(tempProduct);

        const productCopy = {
            ...tempProduct,
        };

        addToCart(productCopy);
        tempProductName.value = null;
        tempProductPrice.value = null;
    };

    const loadFromServer = async () => {
        try {
            const response = await axios.get('/api/productsShow');
            products.value = response.data.products
                .data;
            categories.value = response.data.categories.data;
            filterByCategory(null);
        } catch (error) {
            console.error(error);
        }
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
        return cart.value.reduce(
            (total, item) => total + item.product.price * item.quantity,
            0
        );
    });

    const changeDue = computed(() => {
        return (
            amountPaidCash.value +
            amountPaidBancontact.value +
            amountPaidCreditcard.value +
            amountPaidVirement.value -
            totalAmount.value
        );
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
        const filtered = Object.values(clients.value).filter((client) =>
            client.name
            .toLowerCase()
            .includes(searchQueryClient.value.toLowerCase())
        );
        filteredClients.value = filtered;
    };
    const addClient = (client) => {
        console.log(client.id);
        client_id.value = client.id;
    };

    const addToCart = (product) => {
        const existingItem = cart.value.find(
            (item) => item.product.id === product.id
        );
        if (existingItem) {
            existingItem.quantity++;
        } else {
            if (typeof product.price !== "undefined") {
                cart.value.push({
                    product: {
                        ...product,
                        price: product.price,
                    },
                    quantity: 1,
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
            products: cart.value.map((item) => ({
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

        console.log("Données à envoyer:", requestData);

        await axios
            .post("/api/sales", requestData)
            .then((response) => {
                resetCart();
                loadFromServer();
                sale_id.value = response.data.sale_id;
                console.log("ID de la vente:", sale_id.value);

                generateTicket(sale_id.value);
            })
            .catch((error) => {
                console.error(
                    "Erreur lors de l'enregistrement de la vente:",
                    error
                );
            });
    };

    watchEffect(() => {
        updateFilteredProducts();
        updateDisplayedProducts();
        updateFilteredClients();
    });

    const resetCart = () => {
        cart.value = [];
        totalAmount.value = 0;
        changeDue.value = 0;
        amountPaidCash.value = 0;
        amountPaidBancontact.value = 0;
        amountPaidCreditcard.value = 0;
        amountPaidVirement.value = 0;
        client_id.value = null;
    };
    const showMore = () => {
        maxDisplayedProducts.value += 6;
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
        generateTicket();
        showConfirmationModal.value = false;
    };

    const generateTicket = (saleId) => {
        axios
            .post("/api/pdf", saleId)
            .then((response) => {
                console.log(response);
            })
            .catch((error) => {
                console.error(
                    "Erreur lors de la génération du ticket de caisse:",
                    error
                );
            });
    };

    const filterByCategory = (categoryId) => {
        if (categoryId === currentCategoryId.value) {
            currentCategoryId.value = null;
        } else {
            currentCategoryId.value = categoryId;
        }
        if (currentCategoryId.value === null) {
            filteredProducts.value = Object.values(products.value);
        } else {
            filteredProducts.value = Object.values(products.value).filter(product => product.category_id ===
                currentCategoryId.value);
        }

        updateFilteredProducts();
        updateDisplayedProducts();
    };

</script>
