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
          placeholder="Digite o nome do produto"
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
            placeholder="0.00"
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
          placeholder="Descreva o produto detalhadamente"
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
            <!-- Área de Upload -->
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
        <div v-if="imagePreviewUrls.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
          <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative">
            <img :src="url" class="w-full h-24 object-cover rounded-lg" alt="Preview da imagem" />
            <button
              @click.prevent="removeImage(index)"
              class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
              title="Remover imagem"
            >
              <span class="sr-only">Remover imagem</span>
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
/**
 * Componente ProductForm
 * 
 * Este componente fornece um formulário para criar ou editar produtos.
 * Possui validação de campos, preview de imagens e comunicação com a API.
 */
import { ref, reactive, onMounted, computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, numeric, minValue } from '@vuelidate/validators';
import { ProductAPI } from '@/services/api';
import { useToast } from 'vue-toast-notification';
import type { Product } from '@/types';

// Sistema de notificações toast
const toast = useToast();

// Props do componente
interface Props {
  initialData?: Product;
  isEditing?: boolean;
  confectioneryId?: number;
}

const props = withDefaults(defineProps<Props>(), {
  initialData: undefined,
  isEditing: false,
  confectioneryId: undefined
});

// Eventos emitidos pelo componente
const emit = defineEmits<{
  (e: 'submit', data: Product): void;
  (e: 'cancel'): void;
}>();

/**
 * Formulário reativo para os dados do produto
 */
const form = reactive({
  name: '',
  price: 0,
  description: '',
  confectionery_id: props.confectioneryId,
  images: [] as File[]
});

/**
 * Regras de validação usando Vuelidate
 */
const rules = {
  name: { required },
  price: { required, numeric, minValue: minValue(0) },
  description: { required },
  confectionery_id: { required }
};

// Instância de validação
const v$ = useVuelidate(rules, form);

// Estados de controle do componente
const isSubmitting = ref(false);
const imagePreviewUrls = ref<string[]>([]);

/**
 * Inicializa os dados do formulário quando é um modo de edição
 */
onMounted(() => {
  if (props.initialData) {
    // Copia os dados do produto para o formulário
    form.name = props.initialData.name;
    form.price = props.initialData.price;
    form.description = props.initialData.description;
    form.confectionery_id = props.initialData.confectionery_id;
    
    // Carrega previews das imagens existentes
    if (props.initialData.images && props.initialData.images.length > 0) {
      imagePreviewUrls.value = props.initialData.images.map((img: any) => img.url);
    }
  }
});

/**
 * Manipula o upload de novas imagens
 * 
 * @param event Evento de alteração do input file
 */
const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (!target.files?.length) return;

  // Valida e adiciona cada arquivo como imagem
  Array.from(target.files).forEach(file => {
    // Verifica se é uma imagem
    if (file.type.startsWith('image/')) {
      // Verifica o tamanho máximo permitido (5MB)
      if (file.size <= 5 * 1024 * 1024) {
        form.images.push(file);
        
        // Cria preview da imagem
        const reader = new FileReader();
        reader.onload = e => {
          if (e.target?.result) {
            imagePreviewUrls.value.push(e.target.result as string);
          }
        };
        reader.readAsDataURL(file);
      } else {
        // Notifica o usuário sobre arquivos muito grandes
        toast.error(`A imagem ${file.name} excede o tamanho máximo de 5MB`, {
          position: 'top-right',
          duration: 5000
        });
      }
    }
  });
  
  // Limpa o input para permitir selecionar os mesmos arquivos novamente
  target.value = '';
};

/**
 * Remove uma imagem da lista
 * 
 * @param index Índice da imagem a ser removida
 */
const removeImage = (index: number) => {
  form.images.splice(index, 1);
  imagePreviewUrls.value.splice(index, 1);
};

/**
 * Envia o formulário para API
 */
const handleSubmit = async () => {
  // Valida o formulário
  const valid = await v$.value.$validate();
  if (!valid) return;

  isSubmitting.value = true;

  try {
    // Prepara os dados do formulário para envio (FormData)
    const formData = new FormData();
    
    // Adiciona os campos básicos do produto
    formData.append('name', form.name);
    formData.append('price', form.price.toString());
    formData.append('description', form.description);
    if (props.confectioneryId) {
      formData.append('confectionery_id', props.confectioneryId.toString());
    }

    // Adiciona as novas imagens
    if (form.images.length > 0) {
      form.images.forEach((file) => {
        if (file instanceof File) {
          formData.append('images[]', file);
        }
      });
    }

    let response;
    if (props.isEditing && props.initialData?.id) {
      // Atualiza um produto existente
      response = await ProductAPI.update(props.initialData.id, formData);
      toast.success(`Produto ${form.name} atualizado com sucesso!`, {
        position: 'top-right',
        duration: 3000
      });
    } else {
      // Cria um novo produto
      response = await ProductAPI.create(formData);
      toast.success(`Produto ${form.name} criado com sucesso!`, {
        position: 'top-right',
        duration: 3000
      });
    }

    // Emite o evento de sucesso com os dados retornados
    emit('submit', response.data);
  } catch (error: any) {
    console.error('Erro ao salvar produto:', error);
    // Exibe mensagem de erro específica, se disponível
    const errorMessage = error.response?.data?.error || `Erro ao salvar produto. Por favor, tente novamente.`;
    toast.error(errorMessage, {
      position: 'top-right',
      duration: 5000
    });
  } finally {
    isSubmitting.value = false;
  }
};
</script>