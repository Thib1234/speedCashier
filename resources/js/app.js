import './bootstrap';
import { createApp } from 'vue'
import CreateProduct from './components/products/CreateProduct.vue'
import ProductsList from './components/products/ProductsList.vue'
import ClientsList from './components/clients/ClientsList.vue'
import CreateClient from './components/clients/CreateClient.vue'
import Heading from './components/layout/Heading.vue'
import Cashier from './components/Cashier.vue'
import Daily from './components/compta/Daily.vue'
import Stats from './components/compta/Stats.vue'

const app = createApp({});

app.component('Heading', Heading);
app.component('cashier', Cashier);
app.component('CreateProduct', CreateProduct);
app.component('ProductsList', ProductsList);
app.component('ClientsList', ClientsList);
app.component('CreateClient', CreateClient);
app.component('Daily', Daily);
app.component('Stats', Stats);


app.mount('#app');