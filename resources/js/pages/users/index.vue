<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import DataTable from './components/Datatable/index.vue'

const page = usePage()

// Props que envía el backend (asegúrate de retornarlos desde UserController@index)
const data = computed(() => page.props.data)
const filter = computed(() => page.props.filter ?? [])
const areasOptions = computed(() => page.props.areasOptions ?? [])
const rolesOptions = computed(() => page.props.rolesOptions ?? [])
const estadosOptions = computed(() => page.props.estadosOptions ?? [])

const breadcrumbs = [{ title: 'Usuarios', href: '/users' }]
</script>

<template>

    <Head title="Usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1 class="text-3xl font-bold">Gestión de Usuarios</h1>

            <!-- Evita renderizar hasta tener los datos -->
            <DataTable v-if="data" :data="data" :filter="filter" :areas-options="areasOptions"
                :roles-options="rolesOptions" :estados-options="estadosOptions" />
            <div v-else class="text-sm text-muted-foreground">Cargando…</div>
        </div>
    </AppLayout>
</template>
