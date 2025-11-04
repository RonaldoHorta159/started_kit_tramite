<script setup>
// 1. Quitamos 'lang="ts"'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import DataTable from './components/DataTable/index.vue'

// 2. Obtenemos TODOS los props que envía EmitirController@index
//    (Asegúrate de haber actualizado tu controlador como te indiqué en el paso anterior)
const { documentos, filters, areas, tiposDocumento } = usePage().props

// 3. Definimos los breadcrumbs sin TypeScript
const breadcrumbs = [
    {
        title: 'Emitir',
        href: '/emitir' // <-- 4. ESTE ES EL CAMBIO que soluciona el error
    }
]
</script>

<template>

    <Head title="Emitir" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4 md:p-8">
            <h1 class="text-3xl font-bold">Gestión de Documentos Emitidos</h1>

            <DataTable :documentos="documentos" :filters="filters" :areas="areas" :tipos-documento="tiposDocumento" />
        </div>
    </AppLayout>
</template>
