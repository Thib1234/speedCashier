<template>
    <form>

        <div class="space-y-12 p-20">
            <h1>Créer un client</h1>
            <div class="border-b border-gray-900/10 pb-12">
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom du client</label>
                        <div class="mt-2">
                            <input v-model="formData.name" type="text" name="name" id="first-name"
                                autocomplete="given-name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</template>

<script setup>
import { ref } from 'vue'

const formData = ref({
	name:'',
})

</script>
