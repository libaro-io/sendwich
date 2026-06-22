export interface ProductOption {
    id: number;
    name: string;
    selected?: boolean;
}

export interface Product {
    id: number;
    name: string;
    description?: string | null;
    price: number;
    store_id: number;
    variable_price?: boolean;
    options: ProductOption[];
}

export interface Store {
    id: number;
    name: string;
    products?: Product[];
    products_count?: number;
    order_count?: number;
}

export interface User {
    id: number;
    name: string;
    dept: number;
    paysBack?: number;
}

export interface Order {
    id: number;
    user_id: number;
    product_id: number | null;
    paid_by: number | null;
    quantity: number;
    total: number;
    label: string | null;
    comment: string | null;
    store_name: string | null;
    delivered_at: string | null;
    departed_at: string | null;
    product: Product | null;
    user: { name: string };
    deliverer?: { name: string } | null;
}

export interface Company {
    id: number;
    [key: string]: unknown;
}

export interface DashboardFilters {
    search?: string;
}