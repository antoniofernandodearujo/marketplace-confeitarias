<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    name: '',
    phone: '',
    cep: '',
    street: '',
    number: '',
    neighborhood: '',
    city: '',
    state: '',
    latitude: '',
    longitude: ''
});

const loading = ref(false);

const searchCep = async () => {
    if (form.value.cep.length !== 8) return;
    
    loading.value = true;
    try {
        const response = await axios.get(`https://viacep.com.br/ws/${form.value.cep}/json/`);
        if (!response.data.erro) {
            form.value.street = response.data.logradouro;
            form.value.neighborhood = response.data.bairro;
            form.value.city = response.data.localidade;
            form.value.state = response.data.uf;
        }
    } catch (error) {
        console.error('Error fetching address:', error);
    } finally {
        loading.value = false;
    }
};

const submitForm = async () => {
    // TODO: Implement form submission
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Cadastro de Confeitaria</h1>
        
        <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome do Estabelecimento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome do Estabelecimento</label>
                    <input
                        type="text"
                        v-model="form.name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefone para Contato</label>
                    <input
                        type="tel"
                        v-model="form.phone"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- CEP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">CEP</label>
                    <input
                        type="text"
                        v-model="form.cep"
                        @blur="searchCep"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        maxlength="8"
                        required
                    />
                </div>

                <!-- Rua -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rua</label>
                    <input
                        type="text"
                        v-model="form.street"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Número -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Número</label>
                    <input
                        type="text"
                        v-model="form.number"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Bairro -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bairro</label>
                    <input
                        type="text"
                        v-model="form.neighborhood"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Cidade -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cidade</label>
                    <input
                        type="text"
                        v-model="form.city"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <input
                        type="text"
                        v-model="form.state"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Latitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Latitude</label>
                    <input
                        type="text"
                        v-model="form.latitude"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Longitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Longitude</label>
                    <input
                        type="text"
                        v-model="form.longitude"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    :disabled="loading"
                >
                    {{ loading ? 'Carregando...' : 'Cadastrar' }}
                </button>
            </div>
        </form>
    </div>
</template>