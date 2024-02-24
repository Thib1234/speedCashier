<template>
    <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
                alt="Your Company" />
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your
                account</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom</label>
                    <div class="mt-2">
                        <input v-model="formData.name" id="name" name="name" type="text" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                    </div>
                    <div class="mt-2">
                        <input v-model="formData.password" id="password" name="password" type="password" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>
                <div>
                    <button type="submit" @click.prevent="connection"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Connection</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import {
    ref
} from 'vue'
const errors = ref({});
let success = ref(false);
const formData = ref({
    name: '',
    password: '',
})

const connection = async () => {
    
    await axios.post('/api/login', formData.value)
        .then((res) => {
            console.log('test');
            if (res && res.data && res.data.user) {
                console.log('Utilisateur connecté:', res.data.user); // Afficher les données de l'utilisateur connecté
                
            } else {
                console.error('Erreur: Impossible de récupérer les données de l\'utilisateur.');
            }
            errors.value = {};
            formData.value = {};
            success.value = true;
            
        })
        .catch((err) => {
            console.error('Erreur lors de la connexion:', err);
            errors.value = err.response.data.errors;
        });

        const userAuthenticatedCookie = document.cookie.user_authenticated;
        console.log('Contenu du cookie :', userAuthenticatedCookie);
}



</script>