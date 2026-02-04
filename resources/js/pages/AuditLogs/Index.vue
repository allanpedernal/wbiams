<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, Filter, ScrollText, Search, User } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Activity, type BreadcrumbItem, type PaginatedData } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
    logNames: string[];
    filters: {
        search?: string;
        log_name?: string;
        causer_id?: string;
        date_from?: string;
        date_to?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Audit Logs', href: '/audit-logs' },
];

const search = ref(props.filters.search ?? '');
const logName = ref(props.filters.log_name ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

const applyFilters = () => {
    router.get(
        '/audit-logs',
        {
            search: search.value || undefined,
            log_name: logName.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

watch([search], () => {
    applyFilters();
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleString();
};

const getLogBadgeVariant = (logName: string | null) => {
    if (logName === 'authentication') return 'default';
    if (logName === 'default') return 'secondary';
    return 'outline';
};

const getEventBadgeVariant = (description: string) => {
    if (description.includes('logged in')) return 'default';
    if (description.includes('logged out')) return 'secondary';
    if (description.includes('created') || description.includes('Created')) return 'default';
    if (description.includes('updated')) return 'secondary';
    if (description.includes('deleted') || description.includes('Deleted')) return 'destructive';
    return 'outline';
};

const clearFilters = () => {
    search.value = '';
    logName.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get('/audit-logs');
};
</script>

<template>
    <Head title="Audit Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <ScrollText class="h-5 w-5" />
                        Audit Logs
                    </CardTitle>
                    <CardDescription>
                        View all system activities and user actions
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="mb-4 flex flex-wrap gap-4">
                        <div class="relative flex-1 min-w-[200px]">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Search activities..."
                                class="pl-9"
                            />
                        </div>
                        <select
                            v-model="logName"
                            @change="applyFilters"
                            class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">All Log Types</option>
                            <option v-for="name in logNames" :key="name" :value="name">
                                {{ name }}
                            </option>
                        </select>
                        <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="dateFrom"
                                type="date"
                                @change="applyFilters"
                                class="w-auto"
                            />
                            <span class="text-muted-foreground">to</span>
                            <Input
                                v-model="dateTo"
                                type="date"
                                @change="applyFilters"
                                class="w-auto"
                            />
                        </div>
                        <Button variant="outline" @click="clearFilters">
                            <Filter class="mr-2 h-4 w-4" />
                            Clear
                        </Button>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="p-3 text-left font-medium">Time</th>
                                    <th class="p-3 text-left font-medium">Log Type</th>
                                    <th class="p-3 text-left font-medium">Description</th>
                                    <th class="p-3 text-left font-medium">User</th>
                                    <th class="p-3 text-left font-medium">Subject</th>
                                    <th class="p-3 text-left font-medium">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="activity in activities.data"
                                    :key="activity.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="p-3 text-sm text-muted-foreground whitespace-nowrap">
                                        {{ formatDate(activity.created_at) }}
                                    </td>
                                    <td class="p-3">
                                        <Badge :variant="getLogBadgeVariant(activity.log_name)">
                                            {{ activity.log_name || 'default' }}
                                        </Badge>
                                    </td>
                                    <td class="p-3">
                                        <Badge :variant="getEventBadgeVariant(activity.description)">
                                            {{ activity.description }}
                                        </Badge>
                                    </td>
                                    <td class="p-3">
                                        <div v-if="activity.causer" class="flex items-center gap-2">
                                            <User class="h-4 w-4 text-muted-foreground" />
                                            <div>
                                                <p class="text-sm font-medium">{{ activity.causer.name }}</p>
                                                <p class="text-xs text-muted-foreground">{{ activity.causer.email }}</p>
                                            </div>
                                        </div>
                                        <span v-else class="text-muted-foreground">System</span>
                                    </td>
                                    <td class="p-3 text-sm">
                                        <span v-if="activity.subject_type" class="font-mono text-xs">
                                            {{ activity.subject_type.split('\\').pop() }} #{{ activity.subject_id }}
                                        </span>
                                        <span v-else class="text-muted-foreground">—</span>
                                    </td>
                                    <td class="p-3">
                                        <Link :href="`/audit-logs/${activity.id}`">
                                            <Button variant="ghost" size="sm">View</Button>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="activities.data.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">
                                        No audit logs found.
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
