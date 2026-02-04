<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Network } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import * as ipAddresses from '@/routes/ip-addresses';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'IP Addresses', href: ipAddresses.index().url },
    { title: 'Add IP Address' },
];

const form = useForm({
    ip_address: '',
    label: '',
    comment: '',
});

const submit = () => {
    form.post(ipAddresses.store().url);
};
</script>

<template>
    <Head title="Add IP Address" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Network class="h-5 w-5" />
                        Add IP Address
                    </CardTitle>
                    <CardDescription>
                        Record a new IPv4 or IPv6 address with a label
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="ip_address">IP Address</Label>
                            <Input
                                id="ip_address"
                                v-model="form.ip_address"
                                type="text"
                                placeholder="e.g., 192.168.1.1 or 2001:db8::1"
                                class="font-mono"
                                :class="{ 'border-destructive': form.errors.ip_address }"
                            />
                            <InputError :message="form.errors.ip_address" />
                        </div>

                        <div class="space-y-2">
                            <Label for="label">Label</Label>
                            <Input
                                id="label"
                                v-model="form.label"
                                type="text"
                                placeholder="e.g., Production Server"
                                :class="{ 'border-destructive': form.errors.label }"
                            />
                            <InputError :message="form.errors.label" />
                        </div>

                        <div class="space-y-2">
                            <Label for="comment">Comment (optional)</Label>
                            <textarea
                                id="comment"
                                v-model="form.comment"
                                rows="3"
                                placeholder="Additional notes about this IP address..."
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-destructive': form.errors.comment }"
                            />
                            <InputError :message="form.errors.comment" />
                        </div>

                        <div class="flex gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save IP Address' }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit(ipAddresses.index().url)"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
