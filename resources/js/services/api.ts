import axios from 'axios';

const api = axios.create({
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export const ConfectioneryAPI = {
    getAll: () => api.get('/confectioneries'),
    show: (id: number) => api.get(`/confectioneries/${id}`),
    create: (data: any) => api.post('/confectioneries', data),
    update: (id: number, data: any) => api.patch(`/confectioneries/${id}`, data),
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
        return api.post(`/products/${id}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            params: {
                _method: 'PATCH'
            }
        });
    },
    delete: (id: number) => api.delete(`/products/${id}`),
};

export const AddressAPI = {
    fetchByCep: (cep: string) => api.get(`/address/cep/${cep}`),
};

export default api;