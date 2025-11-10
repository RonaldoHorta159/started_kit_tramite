<script setup>
import { FlexRender } from '@tanstack/vue-table'
import { ref, toRef } from 'vue'
import { router } from '@inertiajs/vue3'
import useBandejaEntradaTable from './useBandejaEntradaTable'
import createColumns from './Columns'
import Toolbar from './Toolbar.vue'
import Pagination from './Pagination.vue'
import DerivarDocumentoModal from '../DerivarDocumentoModal.vue' // Importar modal
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const props = defineProps({
    data: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    estadosOptions: { type: Array, default: () => [] },
    areasOptions: { type: Array, default: () => [] }, // Necesitamos las áreas para el modal
})

// Refs para los modales
const showDerivarModal = ref(false)
const documentoADerivar = ref(null)

// Acciones
function handleRecibir(item) {
    router.post(`/bandeja-entrada/${item.id}/recibir`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ preserveScroll: true });
        },
    })
}
function handleDerivar(item) {
    documentoADerivar.value = item
    showDerivarModal.value = true
}
function handleArchivar(item) { console.log('Archivar:', item) }
function handleVerTrazabilidad(item) { console.log('Ver Trazabilidad:', item) }

const {
    table,
    pageSizes,
    estadoSet,
    toggleEstado,
    clearEstado,
} = useBandejaEntradaTable(toRef(props, 'data'), toRef(props, 'filter'), {
    createColumns,
    onRecibir: handleRecibir,
    onDerivar: handleDerivar,
    onArchivar: handleArchivar,
    onVerTrazabilidad: handleVerTrazabilidad,
})

</script>

<template>
    <div class="w-full">
        <Toolbar :table="table" :estadoSet="estadoSet" :estados-options="estadosOptions" @toggleEstado="toggleEstado"
            @clearEstado="clearEstado" />

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="hg in table.getHeaderGroups()" :key="hg.id">
                        <TableHead v-for="header in hg.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableRow :data-state="row.getIsSelected() && 'selected'">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>
                    <TableRow v-else>
                        <TableCell :colspan="table.getAllColumns().length" class="h-24 text-center">
                            No hay documentos en su bandeja.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <Pagination :page="table.getState().pagination.pageIndex + 1" :pageSize="table.getState().pagination.pageSize"
            :total="data.total ?? 0" :pageSizeOptions="pageSizes" @update:page="(p) => table.setPageIndex(p - 1)"
            @update:pageSize="(s) => table.setPageSize(s)" />

        <!-- Aquí irán los modales para Derivar, Archivar, etc. -->
        <DerivarDocumentoModal v-model:open="showDerivarModal" :documento="documentoADerivar"
            :areas-options="areasOptions" @submitted="() => { /* El POST ya refresca la página */ }"
            @update:open="(val) => { if (!val) documentoADerivar.value = null }" />
    </div>
</template>
