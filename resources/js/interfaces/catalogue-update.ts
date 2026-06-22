export interface Store {
    id: number;
    name: string;
}

export interface NewProduct {
    add: boolean;
    name: string;
    store_id: number | null;
    price: number | string;
}

export interface PriceChange {
    product_id: number;
    name: string;
    store_id: number;
    current_price: number;
    new_price: number | string;
    apply: boolean;
}