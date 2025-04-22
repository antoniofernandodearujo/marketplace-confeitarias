<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md">
      <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-gray-800 transition-all duration-300 hover:text-gray-600">
            <span class="mr-2">🧁</span> Confeitaria Manager
          </h1>
          <nav>
            <ul class="flex space-x-6">
              <li v-for="(tab, index) in tabs" :key="index">
                <button 
                  @click="activeTab = tab.id" 
                  class="px-3 py-2 rounded-md transition-all duration-300"
                  :class="activeTab === tab.id ? 'bg-gray-800 text-white' : 'text-gray-700 hover:bg-gray-100'"
                >
                  {{ tab.name }}
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <!-- Confeitarias Tab -->
      <div v-if="activeTab === 'confeitarias'" class="space-y-8">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Cadastro de Confeitarias</h2>
          <button 
            @click="showConfeitariaForm = !showConfeitariaForm" 
            class="px-4 py-2 bg-gray-800 text-white rounded-md shadow-md hover:bg-gray-700 transition-all duration-300 flex items-center"
          >
            <span v-if="!showConfeitariaForm">Nova Confeitaria</span>
            <span v-else>Cancelar</span>
          </button>
        </div>

        <!-- Confeitaria Form -->
        <div v-if="showConfeitariaForm" class="bg-white p-6 rounded-lg shadow-md transition-all duration-500 animate-fadeIn">
          <h3 class="text-lg font-medium text-gray-800 mb-4">{{ editingConfeitaria ? 'Editar' : 'Nova' }} Confeitaria</h3>
          <form @submit.prevent="saveConfeitaria" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Estabelecimento</label>
                <input 
                  type="text" 
                  id="nome" 
                  v-model="confeitariaForm.nome" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>
              
              <div class="space-y-2">
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone para Contato</label>
                <input 
                  type="tel" 
                  id="telefone" 
                  v-model="confeitariaForm.telefone" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                <div class="flex space-x-2">
                  <input 
                    type="text" 
                    id="cep" 
                    v-model="confeitariaForm.cep" 
                    @blur="buscarCep"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                    required
                  />
                  <button 
                    type="button" 
                    @click="buscarCep" 
                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-all duration-300"
                  >
                    Buscar
                  </button>
                </div>
                <p v-if="cepLoading" class="text-sm text-gray-500">Buscando CEP...</p>
                <p v-if="cepError" class="text-sm text-red-500">{{ cepError }}</p>
              </div>

              <div class="space-y-2">
                <label for="rua" class="block text-sm font-medium text-gray-700">Rua</label>
                <input 
                  type="text" 
                  id="rua" 
                  v-model="confeitariaForm.endereco.rua" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
                <input 
                  type="text" 
                  id="numero" 
                  v-model="confeitariaForm.endereco.numero" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
                <input 
                  type="text" 
                  id="bairro" 
                  v-model="confeitariaForm.endereco.bairro" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                <input 
                  type="text" 
                  id="cidade" 
                  v-model="confeitariaForm.endereco.cidade" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <input 
                  type="text" 
                  id="estado" 
                  v-model="confeitariaForm.endereco.estado" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                <input 
                  type="text" 
                  id="latitude" 
                  v-model="confeitariaForm.latitude" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                <input 
                  type="text" 
                  id="longitude" 
                  v-model="confeitariaForm.longitude" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button 
                type="button" 
                @click="resetConfeitariaForm" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-all duration-300"
              >
                Limpar
              </button>
              <button 
                type="submit" 
                class="px-4 py-2 bg-gray-800 text-white rounded-md shadow-md hover:bg-gray-700 transition-all duration-300"
              >
                {{ editingConfeitaria ? 'Atualizar' : 'Cadastrar' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Confeitarias List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Confeitarias Cadastradas</h3>
          </div>
          <div v-if="confeitarias.length === 0" class="p-6 text-center text-gray-500">
            Nenhuma confeitaria cadastrada.
          </div>
          <div v-else class="divide-y divide-gray-200">
            <div v-for="(confeitaria, index) in confeitarias" :key="index" class="p-4 hover:bg-gray-50 transition-colors duration-200">
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="text-lg font-medium text-gray-800">{{ confeitaria.nome }}</h4>
                  <p class="text-sm text-gray-600">{{ confeitaria.endereco.rua }}, {{ confeitaria.endereco.numero }} - {{ confeitaria.endereco.bairro }}</p>
                  <p class="text-sm text-gray-600">{{ confeitaria.endereco.cidade }}/{{ confeitaria.endereco.estado }} - CEP: {{ confeitaria.cep }}</p>
                  <p class="text-sm text-gray-600">Tel: {{ confeitaria.telefone }}</p>
                </div>
                <div class="flex space-x-2">
                  <button 
                    @click="editConfeitaria(index)" 
                    class="p-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                  >
                    ✏️
                  </button>
                  <button 
                    @click="removeConfeitaria(index)" 
                    class="p-2 text-gray-600 hover:text-red-600 transition-colors duration-200"
                  >
                    🗑️
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Produtos Tab -->
      <div v-if="activeTab === 'produtos'" class="space-y-8">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Gestão de Produtos</h2>
          <button 
            @click="showProdutoForm = !showProdutoForm" 
            class="px-4 py-2 bg-gray-800 text-white rounded-md shadow-md hover:bg-gray-700 transition-all duration-300 flex items-center"
            :disabled="confeitarias.length === 0"
          >
            <span v-if="!showProdutoForm">Novo Produto</span>
            <span v-else>Cancelar</span>
          </button>
        </div>

        <div v-if="confeitarias.length === 0" class="bg-white p-6 rounded-lg shadow-md text-center">
          <p class="text-gray-600">Você precisa cadastrar uma confeitaria antes de adicionar produtos.</p>
        </div>

        <!-- Produto Form -->
        <div v-if="showProdutoForm && confeitarias.length > 0" class="bg-white p-6 rounded-lg shadow-md transition-all duration-500 animate-fadeIn">
          <h3 class="text-lg font-medium text-gray-800 mb-4">{{ editingProduto ? 'Editar' : 'Novo' }} Produto</h3>
          <form @submit.prevent="saveProduto" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="confeitaria" class="block text-sm font-medium text-gray-700">Confeitaria</label>
                <select 
                  id="confeitaria" 
                  v-model="produtoForm.confeitariaId" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                >
                  <option value="" disabled>Selecione uma confeitaria</option>
                  <option v-for="(confeitaria, index) in confeitarias" :key="index" :value="index">
                    {{ confeitaria.nome }}
                  </option>
                </select>
              </div>

              <div class="space-y-2">
                <label for="nomeProduto" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                <input 
                  type="text" 
                  id="nomeProduto" 
                  v-model="produtoForm.nome" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2">
                <label for="valor" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
                <input 
                  type="number" 
                  id="valor" 
                  v-model="produtoForm.valor" 
                  step="0.01" 
                  min="0" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                />
              </div>

              <div class="space-y-2 md:col-span-2">
                <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea 
                  id="descricao" 
                  v-model="produtoForm.descricao" 
                  rows="3" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400"
                  required
                ></textarea>
              </div>

              <div class="space-y-2 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Imagens</label>
                <div class="border-2 border-dashed border-gray-300 rounded-md p-4">
                  <div class="flex items-center justify-center">
                    <label for="fileUpload" class="cursor-pointer bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300 transition-all duration-300">
                      Adicionar Imagens
                      <input 
                        type="file" 
                        id="fileUpload" 
                        @change="handleFileUpload" 
                        multiple 
                        accept="image/*" 
                        class="hidden"
                      />
                    </label>
                  </div>
                  <p class="text-xs text-gray-500 text-center mt-2">Arraste e solte ou clique para selecionar</p>
                </div>
              </div>

              <div v-if="produtoForm.imagens.length > 0" class="md:col-span-2">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-2">
                  <div v-for="(imagem, idx) in produtoForm.imagens" :key="idx" class="relative group">
                    <img 
                      :src="imagem.url" 
                      alt="Preview" 
                      class="h-24 w-full object-cover rounded-md border border-gray-300"
                    />
                    <button 
                      type="button" 
                      @click="removeImagem(idx)" 
                      class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button 
                type="button" 
                @click="resetProdutoForm" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-all duration-300"
              >
                Limpar
              </button>
              <button 
                type="submit" 
                class="px-4 py-2 bg-gray-800 text-white rounded-md shadow-md hover:bg-gray-700 transition-all duration-300"
              >
                {{ editingProduto ? 'Atualizar' : 'Cadastrar' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Produtos List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Produtos Cadastrados</h3>
          </div>
          <div v-if="produtos.length === 0" class="p-6 text-center text-gray-500">
            Nenhum produto cadastrado.
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
            <div v-for="(produto, index) in produtos" :key="index" class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
              <div class="relative h-48 bg-gray-100">
                <div v-if="produto.imagens.length > 0" class="h-full">
                  <div class="swiper-container h-full">
                    <div class="flex h-full">
                      <img 
                        :src="produto.imagens[currentImageIndex[index] || 0].url" 
                        alt="Produto" 
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <div v-if="produto.imagens.length > 1" class="absolute bottom-2 left-0 right-0 flex justify-center space-x-1">
                      <button 
                        v-for="(_, imgIdx) in produto.imagens" 
                        :key="imgIdx" 
                        @click="currentImageIndex[index] = imgIdx" 
                        class="w-2 h-2 rounded-full"
                        :class="(currentImageIndex[index] || 0) === imgIdx ? 'bg-gray-800' : 'bg-gray-400'"
                      ></button>
                    </div>
                  </div>
                </div>
                <div v-else class="flex items-center justify-center h-full text-gray-400">
                  Sem imagem
                </div>
              </div>
              <div class="p-4">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="text-lg font-medium text-gray-800">{{ produto.nome }}</h4>
                    <p class="text-sm text-gray-600">{{ confeitarias[produto.confeitariaId].nome }}</p>
                    <p class="text-sm font-semibold text-gray-800 mt-1">R$ {{ produto.valor.toFixed(2) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ produto.descricao }}</p>
                  </div>
                  <div class="flex space-x-2">
                    <button 
                      @click="editProduto(index)" 
                      class="p-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                    >
                      ✏️
                    </button>
                    <button 
                      @click="removeProduto(index)" 
                      class="p-2 text-gray-600 hover:text-red-600 transition-colors duration-200"
                    >
                      🗑️
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mapa Tab -->
      <div v-if="activeTab === 'mapa'" class="space-y-8">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Visualização em Mapa</h2>
        </div>

        <div v-if="confeitarias.length === 0" class="bg-white p-6 rounded-lg shadow-md text-center">
          <p class="text-gray-600">Nenhuma confeitaria cadastrada para exibir no mapa.</p>
        </div>
        <div v-else class="bg-white p-4 rounded-lg shadow-md">
          <div id="map" class="h-[500px] w-full rounded-md"></div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
      <div class="container mx-auto px-4">
        <p class="text-center text-gray-600 text-sm">© {{ new Date().getFullYear() }} Confeitaria Manager. Todos os direitos reservados.</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Tipos
interface Endereco {
  rua: string;
  numero: string;
  bairro: string;
  cidade: string;
  estado: string;
}

interface Confeitaria {
  nome: string;
  latitude: string;
  longitude: string;
  cep: string;
  endereco: Endereco;
  telefone: string;
}

interface Imagem {
  url: string;
  file?: File;
}

interface Produto {
  nome: string;
  valor: number;
  descricao: string;
  confeitariaId: number;
  imagens: Imagem[];
}

// Estado
const activeTab = ref('confeitarias');
const tabs = [
  { id: 'confeitarias', name: 'Confeitarias' },
  { id: 'produtos', name: 'Produtos' },
  { id: 'mapa', name: 'Mapa' }
];

// Confeitarias
const confeitarias = ref<Confeitaria[]>([]);
const showConfeitariaForm = ref(false);
const editingConfeitaria = ref<number | null>(null);
const cepLoading = ref(false);
const cepError = ref('');

const confeitariaForm = reactive<Confeitaria>({
  nome: '',
  latitude: '',
  longitude: '',
  cep: '',
  endereco: {
    rua: '',
    numero: '',
    bairro: '',
    cidade: '',
    estado: ''
  },
  telefone: ''
});

// Produtos
const produtos = ref<Produto[]>([]);
const showProdutoForm = ref(false);
const editingProduto = ref<number | null>(null);
const currentImageIndex = ref<Record<number, number>>({});

const produtoForm = reactive<Produto>({
  nome: '',
  valor: 0,
  descricao: '',
  confeitariaId: 0,
  imagens: []
});

// Mapa
let map: any = null;
let markers: any[] = [];

// Métodos para Confeitarias
const resetConfeitariaForm = () => {
  confeitariaForm.nome = '';
  confeitariaForm.latitude = '';
  confeitariaForm.longitude = '';
  confeitariaForm.cep = '';
  confeitariaForm.endereco.rua = '';
  confeitariaForm.endereco.numero = '';
  confeitariaForm.endereco.bairro = '';
  confeitariaForm.endereco.cidade = '';
  confeitariaForm.endereco.estado = '';
  confeitariaForm.telefone = '';
  editingConfeitaria.value = null;
  cepError.value = '';
};

const buscarCep = async () => {
  const cep = confeitariaForm.cep.replace(/\D/g, '');
  
  if (cep.length !== 8) {
    cepError.value = 'CEP inválido';
    return;
  }
  
  cepLoading.value = true;
  cepError.value = '';
  
  try {
    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
    const data = await response.json();
    
    if (data.erro) {
      cepError.value = 'CEP não encontrado';
    } else {
      confeitariaForm.endereco.rua = data.logradouro;
      confeitariaForm.endereco.bairro = data.bairro;
      confeitariaForm.endereco.cidade = data.localidade;
      confeitariaForm.endereco.estado = data.uf;
      
      // Buscar coordenadas aproximadas
      try {
        const geoResponse = await fetch(`https://nominatim.openstreetmap.org/search?street=${data.logradouro}&city=${data.localidade}&state=${data.uf}&country=Brazil&format=json`);
        const geoData = await geoResponse.json();
        
        if (geoData.length > 0) {
          confeitariaForm.latitude = geoData[0].lat;
          confeitariaForm.longitude = geoData[0].lon;
        }
      } catch (error) {
        console.error('Erro ao buscar coordenadas:', error);
      }
    }
  } catch (error) {
    cepError.value = 'Erro ao buscar CEP';
    console.error('Erro:', error);
  } finally {
    cepLoading.value = false;
  }
};

const saveConfeitaria = () => {
  const confeitaria = {
    nome: confeitariaForm.nome,
    latitude: confeitariaForm.latitude,
    longitude: confeitariaForm.longitude,
    cep: confeitariaForm.cep,
    endereco: { ...confeitariaForm.endereco },
    telefone: confeitariaForm.telefone
  };
  
  if (editingConfeitaria.value !== null) {
    confeitarias.value[editingConfeitaria.value] = confeitaria;
  } else {
    confeitarias.value.push(confeitaria);
  }
  
  resetConfeitariaForm();
  showConfeitariaForm.value = false;
  
  // Atualizar mapa se estiver visível
  if (activeTab.value === 'mapa' && map) {
    updateMapMarkers();
  }
};

const editConfeitaria = (index: number) => {
  const confeitaria = confeitarias.value[index];
  confeitariaForm.nome = confeitaria.nome;
  confeitariaForm.latitude = confeitaria.latitude;
  confeitariaForm.longitude = confeitaria.longitude;
  confeitariaForm.cep = confeitaria.cep;
  confeitariaForm.endereco = { ...confeitaria.endereco };
  confeitariaForm.telefone = confeitaria.telefone;
  
  editingConfeitaria.value = index;
  showConfeitariaForm.value = true;
};

const removeConfeitaria = (index: number) => {
  if (confirm('Tem certeza que deseja remover esta confeitaria? Todos os produtos associados também serão removidos.')) {
    // Remover produtos associados
    produtos.value = produtos.value.filter(produto => produto.confeitariaId !== index);
    
    // Remover a confeitaria
    confeitarias.value.splice(index, 1);
    
    // Atualizar mapa se estiver visível
    if (activeTab.value === 'mapa' && map) {
      updateMapMarkers();
    }
  }
};

// Métodos para Produtos
const resetProdutoForm = () => {
  produtoForm.nome = '';
  produtoForm.valor = 0;
  produtoForm.descricao = '';
  produtoForm.confeitariaId = 0;
  produtoForm.imagens = [];
  editingProduto.value = null;
};

const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    const files = Array.from(input.files);
    
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        produtoForm.imagens.push({
          url: e.target?.result as string,
          file: file
        });
      };
      reader.readAsDataURL(file);
    });
  }
};

const removeImagem = (index: number) => {
  produtoForm.imagens.splice(index, 1);
};

const saveProduto = () => {
  const produto = {
    nome: produtoForm.nome,
    valor: produtoForm.valor,
    descricao: produtoForm.descricao,
    confeitariaId: produtoForm.confeitariaId,
    imagens: [...produtoForm.imagens]
  };
  
  if (editingProduto.value !== null) {
    produtos.value[editingProduto.value] = produto;
  } else {
    produtos.value.push(produto);
  }
  
  resetProdutoForm();
  showProdutoForm.value = false;
};

const editProduto = (index: number) => {
  const produto = produtos.value[index];
  produtoForm.nome = produto.nome;
  produtoForm.valor = produto.valor;
  produtoForm.descricao = produto.descricao;
  produtoForm.confeitariaId = produto.confeitariaId;
  produtoForm.imagens = [...produto.imagens];
  
  editingProduto.value = index;
  showProdutoForm.value = true;
};

const removeProduto = (index: number) => {
  if (confirm('Tem certeza que deseja remover este produto?')) {
    produtos.value.splice(index, 1);
  }
};

// Métodos para o Mapa
const initMap = () => {
  if (!map) {
    // Coordenadas do Brasil
    const brasilCoords = [-15.77972, -47.92972];
    
    // Criar o mapa com Leaflet
    map = L.map('map').setView(brasilCoords, 4);
    
    // Adicionar camada de tiles do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Adicionar marcadores para as confeitarias
    updateMapMarkers();
  }
};

const updateMapMarkers = () => {
  // Limpar marcadores existentes
  if (markers.length) {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
  }
  
  // Adicionar novos marcadores
  confeitarias.value.forEach((confeitaria, index) => {
    if (confeitaria.latitude && confeitaria.longitude) {
      try {
        const lat = parseFloat(confeitaria.latitude);
        const lng = parseFloat(confeitaria.longitude);
        
        if (!isNaN(lat) && !isNaN(lng)) {
          // Criar ícone personalizado
          const confeitariaIcon = L.icon({
            iconUrl: 'https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/images/marker-icon.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowUrl: 'https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/images/marker-shadow.png',
            shadowSize: [41, 41]
          });
          
          // Criar marcador com popup
          const marker = L.marker([lat, lng], { icon: confeitariaIcon })
            .addTo(map)
            .bindPopup(`
              <div class="p-2">
                <h3 class="font-bold">${confeitaria.nome}</h3>
                <p>${confeitaria.endereco.rua}, ${confeitaria.endereco.numero}</p>
                <p>${confeitaria.endereco.bairro}, ${confeitaria.endereco.cidade}/${confeitaria.endereco.estado}</p>
                <p>Tel: ${confeitaria.telefone}</p>
                <p class="mt-2"><strong>Produtos:</strong> ${produtos.value.filter(p => p.confeitariaId === index).length}</p>
              </div>
            `);
          
          markers.push(marker);
        }
      } catch (error) {
        console.error('Erro ao adicionar marcador:', error);
      }
    }
  });
  
  // Ajustar visualização se houver marcadores
  if (markers.length > 0) {
    const group = L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.1));
  }
};

// Observadores
watch(activeTab, (newTab) => {
  if (newTab === 'mapa') {
    // Inicializar mapa quando a aba for selecionada
    setTimeout(() => {
      initMap();
    }, 100);
  }
});

// Ciclo de vida
onMounted(() => {
  // Carregar dados do localStorage se existirem
  const savedConfeitarias = localStorage.getItem('confeitarias');
  const savedProdutos = localStorage.getItem('produtos');
  
  if (savedConfeitarias) {
    confeitarias.value = JSON.parse(savedConfeitarias);
  }
  
  if (savedProdutos) {
    produtos.value = JSON.parse(savedProdutos);
  }
});

// Salvar dados no localStorage quando houver mudanças
watch([confeitarias, produtos], () => {
  localStorage.setItem('confeitarias', JSON.stringify(confeitarias.value));
  localStorage.setItem('produtos', JSON.stringify(produtos.value));
}, { deep: true });
</script>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-in-out;
}

/* Estilos para o mapa */
.leaflet-container {
  font-family: inherit;
}

.leaflet-popup-content {
  font-family: inherit;
  font-size: 14px;
}
</style>