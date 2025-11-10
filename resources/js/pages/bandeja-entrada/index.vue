<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import DataTable from './components/DataTable/index.vue'

const { props } = usePage()
const data = props.data
const filter = props.filter ?? {}
const estadosOptions = props.estadosOptions ?? []

const breadcrumbs = [{ title: 'Bandeja de Entrada', href: '/bandeja-entrada' }]
</script>

<template>
    <Head title="Bandeja de Entrada" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1 class="text-3xl font-bold">Bandeja de Entrada</h1>
            <DataTable :data="data" :filter="filter" :estados-options="estadosOptions" />
        </div>
    </AppLayout>
</template>