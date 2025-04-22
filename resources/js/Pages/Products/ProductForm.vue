<script setup lang="ts">
import { ref } from 'vue';

interface Confectionery {
    id: number;
    name: string;
}

const form = ref({
    name: '',
    price: '',
    description: '',
    confectionery_id: '',
    images: [] as File[]
});

const previewImages = ref<string[]>([]);
const loading = ref(false);
const confectioneries = ref<Confectionery[]>([]);

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files) return;

    Array.from(target.files).forEach(file => {
        form.value.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            if (e.target?.result) {
                previewImages.value.push(e.target.result as string);
            }
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index: number) => {
    form.value.images.splice(index, 1);
    previewImages.value.splice(index, 1);
};

const submitForm = async () => {
    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('price', form.value.price);
        formData.append('description', form.value.description);
        formData.append('confectionery_id', form.value.confectionery_id);
        
        form.value.images.forEach((image, index) => {
            formData.append(`images[${index}]`, image);
        });

        // TODO: Implement API call
    } catch (error) {
        console.error('Error submitting form:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Cadastro de Produto</h1>
        
        <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Confeitaria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confeitaria</label>
                    <select
                        v-model="form.confectionery_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="" disabled>Selecione uma confeitaria</option>
                        <option v-for="conf in confectioneries" :key="conf.id" :value="conf.id">
                            {{ conf.name }}
                        </option>
                    </select>
                </div>

                <!-- Nome do Produto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                    <input
                        type="text"
                        v-model="form.name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Preço -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor</label>
                    <input
                        type="number"
                        step="0.01"
                        v-model="form.price"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    ></textarea>
                </div>

                <!-- Upload de Imagens -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Imagens do Produto</label>
                    <div class="mt-2">
                        <input
                            type="file"
                            @change="handleImageUpload"
                            multiple
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                    </div>

                    <!-- Preview de Imagens -->
                    <div v-if="previewImages.length" class="mt-4 grid grid-cols-3 gap-4">
                        <div v-for="(preview, index) in previewImages" :key="index" class="relative">
                            <img :src="preview" class="w-full h-32 object-cover rounded-lg" />
                            <button
                                type="button"
                                @click="removeImage(index)"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    :disabled="loading"
                >
                    {{ loading ? 'Carregando...' : 'Cadastrar Produto' }}
                </button>
            </div>
        </form>
    </div>
</template>