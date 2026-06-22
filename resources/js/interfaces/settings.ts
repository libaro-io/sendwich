export interface ChannelConfiguration {
    webhook_url: string;
}

export interface NotificationChannel {
    id: number | null;
    driver: string;
    configuration: ChannelConfiguration;
    enabled: boolean;
}

export interface NotificationDriver {
    value: string;
    label: string;
}

export interface Company {
    select_runner_at?: string | null;
    auto_assign_runner?: boolean;
    reminder_enabled?: boolean;
    reminder_time?: string | null;
    reminder_days?: number[];
    notification_channels?: NotificationChannel[];
    [key: string]: unknown;
}