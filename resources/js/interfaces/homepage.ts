export type Tint = 'teal' | 'coral' | 'sun';

export interface Complaint {
    avatar: string;
    text: string;
    tint: Tint;
    rot: string;
}

export interface OrderLine {
    avatar: string;
    name: string;
    text: string;
    note: string;
}

export interface Perk {
    tint: Tint;
    icon: string;
    title: string;
    tag: string;
    desc: string;
}

export interface Step {
    n: string;
    tint: Tint;
    title: string;
    desc: string;
}
