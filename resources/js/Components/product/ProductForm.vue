<template>
  <div class="bg-white p-6 rounded-lg">
    <h3 class="text-xl font-semibold mb-6">{{ isEditing ? 'Editar' : 'Novo' }} Produto</h3>
    
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Nome do Produto -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
        <input 
          type="text" 
          id="name" 
          v-model="form.name"
          :class="{'border-red-500': v$.name.$error}"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          required
        />
        <span v-if="v$.name.$error" class="text-xs text-red-500">{{ v$.name.$errors[0].$message }}</span>
      </div>

      <!-- Preço -->
      <div>
        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço</label>
        <div class="relative">
          <span class="absolute left-3 top-2 text-gray-500">R$</span>
          <input 
            type="number" 
            id="price" 
            v-model="form.price"
            step="0.01"
            min="0"
            :class="{'border-red-500': v$.price.$error}"
            class="w-full pl-12 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
        </div>
        <span v-if="v$.price.$error" class="text-xs text-red-500">{{ v$.price.$errors[0].$message }}</span>
      </div>

      <!-- Descrição -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea 
          id="description" 
          v-model="form.description"
          :class="{'border-red-500': v$.description.$error}"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          rows="4"
          required
        ></textarea>
        <span v-if="v$.description.$error" class="text-xs text-red-500">{{ v$.description.$errors[0].$message }}</span>
      </div>

      <!-- Upload de Imagens -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Imagens do Produto</label>
        <div class="flex items-center justify-center w-full">
          <label
            class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
          >
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
              <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <p class="mb-2 text-sm text-gray-500">Clique para fazer upload</p>
              <p class="text-xs text-gray-500">PNG, JPG até 5MB</p>
            </div>
            <input 
              type="file" 
              class="hidden" 
              accept="image/*"
              multiple
              @change="handleImageUpload"
            />
          </label>
        </div>
        
        <!-- Preview de Imagens -->
        <div v-if="imagePreviewUrls.length > 0" class="mt-4 grid grid-cols-3 gap-4">
          <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative">
            <img :src="url" class="w-full h-24 object-cover rounded-lg" />
            <button
              @click.prevent="removeImage(index)"
              class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Botões -->
      <div class="flex justify-end space-x-3 pt-6">
        <button 
          type="button" 
          @click="$emit('cancel')"
          class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Cancelar
        </button>
        <button 
          type="submit"
          :disabled="isSubmitting"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          {{ isSubmitting ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Cadastrar') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, numeric, minValue } from '@vuelidate/validators';
import { ProductAPI } from '@/services/api';

const props = withDefaults(defineProps<{
  initialData?: any;
  isEditing?: boolean;
  confectioneryId?: number;
}>(), {
  initialData: undefined,
  isEditing: false,
  confectioneryId: undefined
});

const emit = defineEmits<{
  (e: 'submit', data: any): void;
  (e: 'cancel'): void;
}>();

const form = reactive({
  name: '',
  price: 0,
  description: '',
  confectionery_id: props.confectioneryId,
  images: [] as File[]
});

// Validação
const rules = {
  name: { required },
  price: { required, numeric, minValue: minValue(0) },
  description: { required },
  confectionery_id: { required }
};

const v$ = useVuelidate(rules, form);

// Estado
const isSubmitting = ref(false);
const imagePreviewUrls = ref<string[]>([]);

onMounted(() => {
  if (props.initialData) {
    Object.assign(form, props.initialData);
    if (props.initialData.images) {
      imagePreviewUrls.value = props.initialData.images.map((img: any) => img.url);
    }
  }
});

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (!target.files?.length) return;

  Array.from(target.files).forEach(file => {
    if (file.type.startsWith('image/')) {
      form.images.push(file);
      const reader = new FileReader();
      reader.onload = e => {
        if (e.target?.result) {
          imagePreviewUrls.value.push(e.target.result as string);
        }
      };
      reader.readAsDataURL(file);
    }
  });
};

const removeImage = (index: number) => {
  form.images.splice(index, 1);
  imagePreviewUrls.value.splice(index, 1);
};

const handleSubmit = async () => {
  const valid = await v$.value.$validate();
  if (!valid) return;

  isSubmitting.value = true;

  try {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('price', form.price.toString());
    formData.append('description', form.description || '');
    formData.append('confectionery_id', props.confectioneryId?.toString() || '');

    // Adiciona as novas imagens
    form.images.forEach((file: File) => {
      if (file instanceof File) {
        formData.append('images[]', file);
      }
    });

    let response;
    if (props.isEditing && props.initialData?.id) {
      response = await ProductAPI.update(props.initialData.id, formData);
    } else {
      response = await ProductAPI.create(formData);
    }

    emit('submit', response.data);
  } catch (error) {
    console.error('Erro ao salvar produto:', error);
    alert('Erro ao salvar produto. Por favor, tente novamente.');
  } finally {
    isSubmitting.value = false;
  }
};
</script>