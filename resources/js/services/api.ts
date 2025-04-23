import axios, { AxiosInstance, AxiosResponse } from 'axios';
import type { Confectionery, Product } from '../types';

/**
 * Instância base do Axios para comunicação com a API
 * 
 * Centraliza configurações comuns como headers e interceptadores
 */
const api: AxiosInstance = axios.create({
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    // Adiciona o token CSRF para proteção contra ataques CSRF
    withCredentials: true,
});

/**
 * Interceptor para tratamento global de erros
 */
api.interceptors.response.use(
    (response) => response,
    (error) => {
        // Tratamento centralizado de erros de API
        // Pode ser expandido para lidar com diferentes códigos de erro
        if (error.response?.status === 401) {
            // Usuário não autenticado - redirecionar para login
            console.error('Sessão expirada ou usuário não autenticado');
            // window.location.href = '/login'; // Descomente para implementar redirecionamento
        }
        return Promise.reject(error);
    }
);

/**
 * Serviços de API para Confeitarias
 */
export const ConfectioneryAPI = {
    /**
     * Obtém todas as confeitarias e seus produtos
     * @returns Promise com lista de confeitarias e produtos
     */
    getAll: (): Promise<AxiosResponse<{confectioneries: Confectionery[], products: Product[]}>> => 
        api.get('/confectioneries'),
    
    /**
     * Obtém detalhes de uma confeitaria específica
     * @param id ID da confeitaria
     */
    show: (id: number): Promise<AxiosResponse<Confectionery>> => 
        api.get(`/confectioneries/${id}`),
    
    /**
     * Cria uma nova confeitaria
     * @param data Dados da confeitaria
     */
    create: (data: any): Promise<AxiosResponse<Confectionery>> => 
        api.post('/confectioneries', data),
    
    /**
     * Atualiza uma confeitaria existente
     * @param id ID da confeitaria
     * @param data Novos dados da confeitaria
     */
    update: (id: number, data: any): Promise<AxiosResponse<Confectionery>> => 
        api.patch(`/confectioneries/${id}`, data),
    
    /**
     * Exclui uma confeitaria
     * @param id ID da confeitaria
     */
    delete: (id: number): Promise<AxiosResponse<void>> => 
        api.delete(`/confectioneries/${id}`),
};

/**
 * Serviços de API para Produtos
 */
export const ProductAPI = {
    /**
     * Cria um novo produto
     * @param data FormData contendo dados do produto e imagens
     */
    create: (data: FormData): Promise<AxiosResponse<Product>> => {
        return api.post('/products', data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
    
    /**
     * Atualiza um produto existente
     * @param id ID do produto
     * @param data FormData contendo dados atualizados do produto e novas imagens
     */
    update: (id: number, data: FormData): Promise<AxiosResponse<Product>> => {
        return api.post(`/products/${id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            params: {
                _method: 'PATCH' // Laravel requer _method para aceitar PATCH com FormData
            }
        });
    },
    
    /**
     * Exclui um produto
     * @param id ID do produto
     */
    delete: (id: number): Promise<AxiosResponse<void>> => api.delete(`/products/${id}`),
};

/**
 * Serviços de API para Endereços
 */
export const AddressAPI = {
    /**
     * Busca endereço a partir do CEP
     * @param cep CEP no formato 00000-000 ou 00000000
     */
    fetchByCep: (cep: string): Promise<AxiosResponse<{
        cep: string;
        street: string;
        neighborhood: string;
        city: string;
        state: string;
    }>> => api.get(`/address/cep/${cep}`),
};

export default api;