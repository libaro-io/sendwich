// The public display screen receives the company together with its public
// access token, used to authenticate the polling endpoints.
export interface DisplayCompany {
    id: number;
    name?: string;
    token: string;
    [key: string]: unknown;
}

export interface Runner {
    name: string;
}