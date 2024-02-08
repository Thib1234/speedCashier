<template>
    
    <form>
		
        <div class="space-y-12 p-20">
			<h1>Créer un utilisateur</h1>

            <div class="border-b border-gray-900/10 pb-12">

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom de
                            l'utilisateur</label>
                        <div class="mt-2">
                            <input v-model="formData.name" type="text" name="name" id="first-name" autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email de l'utilisateur
                            address</label>
                        <div class="mt-2">
                            <input v-model="formData.email" id="email" name="email" type="email" autocomplete="email"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

					<div class="sm:col-span-4">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Mot de passe</label>
                        <div class="mt-2">
                            <input v-model="formData.password" id="password" name="password" type="password" autocomplete="password"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

					<div class="sm:col-span-4">
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirmation du mot de passe</label>
                        <div class="mt-2">
                            <input v-model="formData.password_confirmation" id="password_confirmation" name="password_confirmation" type="password" autocomplete="password_confirmation"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </form>
</template>

<script setup>

const formData = ref({
	name:'',
	email:'',
	password:'',
	password_confirmation:'',
})

</script>
