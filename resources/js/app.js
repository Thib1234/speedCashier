import './bootstrap';
import { createApp } from 'vue'
import CreateProduct from './components/products/CreateProduct.vue'
import ProductsList from './components/products/ProductsList.vue'
import ClientsList from './components/clients/ClientsList.vue'
import CreateClient from './components/clients/CreateClient.vue'
import Heading from './components/layout/Heading.vue'
import Cashier from './components/Cashier.vue'
import CashierTest from './components/CashierTest.vue'
import Daily from './components/compta/Daily.vue'
import DailyToilettage from './components/compta/DailyToilettage.vue'
import DailyAccessories from './components/compta/DailyAccessories.vue'
import Stats from './components/compta/Stats.vue'
import StatsToilettage from './components/compta/StatsToilettage.vue'
import Show from './components/compta/Show.vue'
import AcountsList from './components/acompte/AccountsList.vue'
import CreateAccount from './components/acompte/CreateAccount.vue'

const app = createApp({});

app.component('Heading', Heading);
app.component('cashier', Cashier);
app.component('CashierTest', CashierTest);
app.component('CreateProduct', CreateProduct);
app.component('ProductsList', ProductsList);
app.component('ClientsList', ClientsList);
app.component('CreateClient', CreateClient);
app.component('Daily', Daily);
app.component('DailyToilettage', DailyToilettage);
app.component('DailyAccessories', DailyAccessories);
app.component('Stats', Stats);
app.component('StatsToilettage', StatsToilettage);
app.component('Show', Show);
app.component('AcountsList', AcountsList);
app.component('CreateAccount', CreateAccount);


app.mount('#app');
