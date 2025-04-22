import axios from 'axios';

const api = axios.create({
    baseURL: '',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export const ConfectioneryAPI = {
    getAll: () => api.get('/confectioneries'),
    get: (id: number) => api.get(`/confectioneries/${id}`),
    create: (data: FormData) => {
        return api.post('/confectioneries', data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
    update: (id: number, data: FormData) => {
        return api.put(`/confectioneries/${id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
    delete: (id: number) => api.delete(`/confectioneries/${id}`),
};

export const ProductAPI = {
    create: (data: FormData) => {
        return api.post('/products', data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
    update: (id: number, data: FormData) => {
        return api.put(`/products/${id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
    delete: (id: number) => api.delete(`/products/${id}`),
};

export const AddressAPI = {
    fetchByCep: (cep: string) => api.get(`/address/cep/${cep}`),
};

export default api;