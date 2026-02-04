export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[];
    permissions?: Permission[];
    [key: string]: unknown;
};

export type Role = {
    id: number;
    name: string;
};

export type Permission = {
    id: number;
    name: string;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};

export type IpAddress = {
    id: number;
    ip_address: string;
    label: string;
    comment: string | null;
    user_id: number;
    user?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
};

export type Activity = {
    id: number;
    log_name: string | null;
    description: string;
    subject_type: string | null;
    subject_id: number | null;
    causer_type: string | null;
    causer_id: number | null;
    properties: Record<string, unknown>;
    causer?: {
        id: number;
        name: string;
        email: string;
    };
    subject?: Record<string, unknown>;
    created_at: string;
    updated_at: string;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
};
