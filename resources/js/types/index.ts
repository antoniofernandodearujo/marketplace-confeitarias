export interface Address {
  street: string;
  number: string;
  neighborhood: string;
  city: string;
  state: string;
}

export interface Confectionery {
  id: number;
  name: string;
  phone: string;
  address: Address;
  latitude: number;
  longitude: number;
}

export interface ProductImage {
  id: number;
  url: string;
}

export interface Product {
  id: number;
  confectionery_id: number;
  name: string;
  description: string;
  price: number;
  images: ProductImage[];
}