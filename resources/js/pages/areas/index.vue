<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { areas } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from "@/components/ui/button"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Areas',
        href: areas().url,
    },
];
</script>

<template>

    <Head title="Areas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="m-10 ">
                    <Dialog>
                        <DialogTrigger as-child>
                            <Button variant="outline">
                                Emitir un tramite
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[425px]">
                            <DialogHeader>
                                <DialogTitle>Edit profile</DialogTitle>
                                <DialogDescription>
                                    Make changes to your profile here. Click save when you're done.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="name" class="text-right">
                                        Name
                                    </Label>
                                    <Input id="name" value="Pedro Duarte" class="col-span-3" />
                                </div>
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="username" class="text-right">
                                        Username
                                    </Label>
                                    <Input id="username" value="@peduarte" class="col-span-3" />
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="submit">
                                    Save changes
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
