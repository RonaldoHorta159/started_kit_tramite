<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import DataTable from './components/Datatable/index.vue'

const page = usePage()

// Props desde el backend (TipoDocumentoController@index)
const data = computed(() => page.props.data)
const filter = computed(() => page.props.filter ?? [])
const estadosOptions = computed(() => page.props.estadosOptions ?? [])

const breadcrumbs = [{ title: 'Tipos de Documento', href: '/tipos-documento' }]
</script>

<template>

    <Head title="Tipos de Documento" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1 class="text-3xl font-bold">Gestión de Tipos de Documento</h1>

            <DataTable v-if="data" :data="data" :filter="filter" :estados-options="estadosOptions" />
            <div v-else class="text-sm text-muted-foreground">Cargando…</div>
        </div>
    </AppLayout>
</template>
