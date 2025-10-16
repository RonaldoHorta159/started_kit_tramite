<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import DataTable from './components/dataTable.vue';

const page = usePage()
const areas = page.props.areas || []

const breadcrumbs = [
    {
        title: 'Areas',
        href: '/areas',
    },
];
</script>

<template>

    <Head title="Areas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1 class="text-3xl font-bold">Gestión de Áreas</h1>
            <DataTable :data="areas" />
        </div>
    </AppLayout>
</template>
