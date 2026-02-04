<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Clock, ScrollText, User } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Activity, type BreadcrumbItem } from '@/types';
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
    activity: Activity;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Audit Logs', href: '/audit-logs' },
    { title: `Activity #${props.activity.id}` },
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
    <Head :title="`Activity #${activity.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Link href="/audit-logs">
                    <Button variant="ghost" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-semibold">Activity Details</h1>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <!-- Activity Details -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <ScrollText class="h-5 w-5" />
                            Activity Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Activity ID</p>
                            <p class="text-lg font-mono">#{{ activity.id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Log Type</p>
                            <Badge variant="outline" class="mt-1">
                                {{ activity.log_name || 'default' }}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Description</p>
                            <Badge :variant="getEventBadgeVariant(activity.description)" class="mt-1">
                                {{ activity.description }}
                            </Badge>
                        </div>
                        <div class="flex items-center gap-2">
                            <Clock class="h-4 w-4 text-muted-foreground" />
                            <span class="text-sm text-muted-foreground">Time</span>
                            <span>{{ formatDate(activity.created_at) }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- User & Subject -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="h-5 w-5" />
                            User & Subject
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Performed By</p>
                            <div v-if="activity.causer" class="mt-1">
                                <p class="text-lg">{{ activity.causer.name }}</p>
                                <p class="text-sm text-muted-foreground">{{ activity.causer.email }}</p>
                            </div>
                            <p v-else class="text-lg text-muted-foreground">System</p>
                        </div>
                        <div v-if="activity.subject_type">
                            <p class="text-sm font-medium text-muted-foreground">Subject</p>
                            <p class="font-mono text-sm mt-1">
                                {{ activity.subject_type.split('\\').pop() }} #{{ activity.subject_id }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Properties -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Properties</CardTitle>
                        <CardDescription>
                            Additional data recorded with this activity
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="activity.properties && Object.keys(activity.properties).length > 0">
                            <div class="rounded bg-muted p-4 overflow-x-auto">
                                <pre class="text-sm font-mono whitespace-pre-wrap">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                            </div>
                        </div>
                        <p v-else class="text-muted-foreground">No additional properties recorded.</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
