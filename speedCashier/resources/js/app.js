import './bootstrap';
import { createApp } from 'vue'
import UsersList from './components/users/UsersList.vue'
import CreateUser from './components/users/CreateUser.vue'
import Heading from './components/layout/Heading.vue'
import Cashier from './components/Cashier.vue'

const app = createApp({});

app.component('Heading', Heading);
app.component('UsersList', UsersList);
app.component('CreateUser', CreateUser);
app.component('cashier', Cashier);


app.mount('#app');