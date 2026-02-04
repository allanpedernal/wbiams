<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Clock, Network, Pencil, User } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import * as ipAddresses from '@/routes/ip-addresses';
import { type Activity, type BreadcrumbItem, type IpAddress } from '@/types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const props = defineProps<{
    ipAddress: IpAddress;
    activities: Activity[];
}>();

const page = usePage();
const isSuperAdmin = (page.props.auth as { user?: { is_super_admin?: boolean } })?.user?.is_super_admin ?? false;
const currentUserId = (page.props.auth as { user?: { id?: number } })?.user?.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'IP Addresses', href: ipAddresses.index().url },
    { title: props.ipAddress.ip_address },
];

const canEdit = isSuperAdmin || props.ipAddress.user_id === currentUserId;

const formatDate = (date: string) => {
    return new Date(date).toLocaleString();
};

const getEventBadgeVariant = (description: string) => {
    if (description.includes('created')) return 'default';
    if (description.includes('updated')) return 'secondary';
    if (description.includes('deleted')) return 'destructive';
    return 'outline';
};
</script>

<template>
    <Head :title="ipAddress.ip_address" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Link :href="ipAddresses.index().url">
                    <Button variant="ghost" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-semibold">IP Address Details</h1>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- IP Address Details -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2">
                                <Network class="h-5 w-5" />
                                Details
                            </CardTitle>
                            <Link v-if="canEdit" :href="ipAddresses.edit(ipAddress.id).url">
                                <Button variant="outline" size="sm">
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Edit
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">IP Address</p>
                            <p class="font-mono text-lg">{{ ipAddress.ip_address }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Label</p>
                            <p class="text-lg">{{ ipAddress.label }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Comment</p>
                            <p class="text-lg">{{ ipAddress.comment || '—' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <User class="h-4 w-4 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Added by</span>
                            <span>{{ ipAddress.user?.name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Clock class="h-4 w-4 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Created</span>
                            <span>{{ formatDate(ipAddress.created_at) }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Activity History -->
                <Card>
                    <CardHeader>
                        <CardTitle>Activity History</CardTitle>
                        <CardDescription>
                            Recent changes to this IP address
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="activities.length === 0" class="text-center text-muted-foreground py-8">
                            No activity recorded yet.
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="activity in activities"
                                :key="activity.id"
                                class="flex items-start gap-3 border-b pb-3 last:border-0"
                            >
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <Badge :variant="getEventBadgeVariant(activity.description)">
                                            {{ activity.description }}
                                        </Badge>
                                    </div>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        by {{ activity.causer?.name || 'System' }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ formatDate(activity.created_at) }}
                                    </p>
                                    <div
                                        v-if="activity.properties && Object.keys(activity.properties).length > 0"
                                        class="mt-2 rounded bg-muted p-2 text-xs font-mono"
                                    >
                                        <pre class="whitespace-pre-wrap">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
