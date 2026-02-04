<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Network, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import * as ipAddressRoutes from '@/routes/ip-addresses';
import { type BreadcrumbItem, type IpAddress, type PaginatedData } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps<{
    ipAddresses?: PaginatedData<IpAddress>;
    filters?: {
        search?: string;
    };
}>();

// Computed property with safe defaults
const ipList = computed(() => props.ipAddresses?.data ?? []);
const paginationMeta = computed(() => ({
    lastPage: props.ipAddresses?.last_page ?? 1,
    from: props.ipAddresses?.from ?? 0,
    to: props.ipAddresses?.to ?? 0,
    total: props.ipAddresses?.total ?? 0,
    links: props.ipAddresses?.links ?? [],
}));

const page = usePage();
const isSuperAdmin = (page.props.auth as { user?: { is_super_admin?: boolean } })?.user?.is_super_admin ?? false;
const currentUserId = (page.props.auth as { user?: { id?: number } })?.user?.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'IP Addresses', href: ipAddressRoutes.index().url },
];

const search = ref(props.filters?.search ?? '');
const deleteDialogOpen = ref(false);
const ipToDelete = ref<IpAddress | null>(null);

watch(search, (value) => {
    router.get(
        ipAddressRoutes.index().url,
        { search: value || undefined },
        { preserveState: true, replace: true }
    );
});

const canEdit = (ip: IpAddress) => isSuperAdmin || ip.user_id === currentUserId;
const canDelete = () => isSuperAdmin;

const confirmDelete = (ip: IpAddress) => {
    ipToDelete.value = ip;
    deleteDialogOpen.value = true;
};

const deleteIpAddress = () => {
    if (ipToDelete.value) {
        router.delete(ipAddressRoutes.destroy(ipToDelete.value.id).url, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                ipToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="IP Addresses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <Network class="h-5 w-5" />
                                IP Addresses
                            </CardTitle>
                            <CardDescription>
                                Manage IP addresses with labels and comments
                            </CardDescription>
                        </div>
                        <Link :href="ipAddressRoutes.create().url">
                            <Button>
                                <Plus class="mr-2 h-4 w-4" />
                                Add IP Address
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative max-w-sm">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Search IP addresses..."
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th class="p-3 text-left font-medium">IP Address</th>
                                    <th class="p-3 text-left font-medium">Label</th>
                                    <th class="p-3 text-left font-medium">Comment</th>
                                    <th class="p-3 text-left font-medium">Added By</th>
                                    <th class="p-3 text-left font-medium">Created</th>
                                    <th class="p-3 text-right font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="ip in ipList"
                                    :key="ip.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="p-3">
                                        <Link
                                            :href="ipAddressRoutes.show(ip.id).url"
                                            class="font-mono text-primary hover:underline"
                                        >
                                            {{ ip.ip_address }}
                                        </Link>
                                    </td>
                                    <td class="p-3">{{ ip.label }}</td>
                                    <td class="p-3 text-muted-foreground">
                                        {{ ip.comment || '—' }}
                                    </td>
                                    <td class="p-3">{{ ip.user?.name }}</td>
                                    <td class="p-3 text-muted-foreground">
                                        {{ new Date(ip.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                v-if="canEdit(ip)"
                                                :href="ipAddressRoutes.edit(ip.id).url"
                                            >
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                v-if="canDelete()"
                                                variant="ghost"
                                                size="icon"
                                                class="text-destructive hover:text-destructive"
                                                @click="confirmDelete(ip)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="ipList.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">
                                        No IP addresses found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="paginationMeta.lastPage > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">
                            Showing {{ paginationMeta.from }} to {{ paginationMeta.to }} of {{ paginationMeta.total }} results
                        </p>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in paginationMeta.links"
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

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete IP Address</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete the IP address
                        <span class="font-mono font-semibold">{{ ipToDelete?.ip_address }}</span>?
                        This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">Cancel</Button>
                    </DialogClose>
                    <Button variant="destructive" @click="deleteIpAddress">
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
