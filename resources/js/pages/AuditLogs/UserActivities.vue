<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Clock, ScrollText, User } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Activity, type BreadcrumbItem, type PaginatedData } from '@/types';
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
    activities: PaginatedData<Activity>;
    userId: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Audit Logs', href: '/audit-logs' },
    { title: `User #${props.userId} Activities` },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleString();
};

const getEventBadgeVariant = (description: string) => {
    if (description.includes('logged in')) return 'default';
    if (description.includes('logged out')) return 'secondary';
    if (description.includes('created') || description.includes('Created')) return 'default';
    if (description.includes('updated')) return 'secondary';
    if (description.includes('deleted') || description.includes('Deleted')) return 'destructive';
    return 'outline';
};
</script>

<template>
    <Head :title="`User Activities`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Link href="/audit-logs">
                    <Button variant="ghost" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-semibold">User Activity History</h1>
                    <p class="text-sm text-muted-foreground">User ID: {{ userId }}</p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <User class="h-5 w-5" />
                        All Activities by User
                    </CardTitle>
                    <CardDescription>
                        Complete history of activities performed by this user
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="p-3 text-left font-medium">Time</th>
                                    <th class="p-3 text-left font-medium">Log Type</th>
                                    <th class="p-3 text-left font-medium">Description</th>
                                    <th class="p-3 text-left font-medium">Subject</th>
                                    <th class="p-3 text-left font-medium">Session</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="activity in activities.data"
                                    :key="activity.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="p-3 text-sm text-muted-foreground whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <Clock class="h-4 w-4" />
                                            {{ formatDate(activity.created_at) }}
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <Badge variant="outline">
                                            {{ activity.log_name || 'default' }}
                                        </Badge>
                                    </td>
                                    <td class="p-3">
                                        <Badge :variant="getEventBadgeVariant(activity.description)">
                                            {{ activity.description }}
                                        </Badge>
                                    </td>
                                    <td class="p-3 text-sm">
                                        <span v-if="activity.subject_type" class="font-mono text-xs">
                                            {{ activity.subject_type.split('\\').pop() }} #{{ activity.subject_id }}
                                        </span>
                                        <span v-else class="text-muted-foreground">—</span>
                                    </td>
                                    <td class="p-3">
                                        <Link
                                            v-if="activity.properties?.session_id"
                                            :href="`/audit-logs/session/${activity.properties.session_id}`"
                                            class="text-xs font-mono text-primary hover:underline"
                                        >
                                            {{ (activity.properties.session_id as string).substring(0, 8) }}...
                                        </Link>
                                        <span v-else class="text-muted-foreground">—</span>
                                    </td>
                                </tr>
                                <tr v-if="activities.data.length === 0">
                                    <td colspan="5" class="p-8 text-center text-muted-foreground">
                                        No activities found for this user.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="activities.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">
                            Showing {{ activities.from }} to {{ activities.to }} of {{ activities.total }} results
                        </p>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in activities.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 text-sm rounded border',
                                    link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted',
                                    !link.url ? 'opacity-50 pointer-events-none' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
