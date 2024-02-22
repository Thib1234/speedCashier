import './bootstrap';
import { createApp } from 'vue'
import CreateUser from './components/users/CreateUser.vue'
import CreateProduct from './components/products/CreateProduct.vue'
import ProductsList from './components/products/ProductsList.vue'
import ClientsList from './components/clients/ClientsList.vue'
import CreateClient from './components/clients/CreateClient.vue'
import Heading from './components/layout/Heading.vue'
import Cashier from './components/Cashier.vue'

const app = createApp({});

app.component('Heading', Heading);
app.component('CreateUser', CreateUser);
app.component('cashier', Cashier);
app.component('CreateProduct', CreateProduct);
app.component('ProductsList', ProductsList);
app.component('ClientsList', ClientsList);
app.component('CreateClient', CreateClient);


app.mount('#app');