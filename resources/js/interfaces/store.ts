import type { Store as DashboardStore } from '@interfaces/dashboard';

// Full store record used by the store admin pages — extends the lightweight
// dashboard Store with the editable detail fields.
export interface Store extends DashboardStore {
    address?: string | null;
    zip?: string | null;
    city?: string | null;
    phone?: string | null;
    email?: string | null;
    website?: string | null;
}

export interface StoreForm {
    name: string;
    address: string;
    zip: string;
    city: string;
    phone: string;
    email: string;
    website: string;
}

export interface NewStoreForm extends StoreForm {
    template: string;
}

export interface NewProductForm {
    name: string;
    description: string;
    price: number;
    variable_price: boolean;
}