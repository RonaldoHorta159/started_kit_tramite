<script setup>
import { ref } from 'vue'
import { FlexRender } from '@tanstack/vue-table'
import useAreasTable from './useAreasTable'
import Toolbar from './Toolbar.vue'
import Pagination from './Pagination.vue'
import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table'
import CreateDialog from './CreateDialog.vue'
import EditDialog from './EditDialog.vue'
import ConfirmDelete from './ConfirmDelete.vue'
import { router } from '@inertiajs/vue3'


const props = defineProps({
    data: { type: Object, required: true },
    filter: { type: Array, default: () => [] },
})

const showCreate = ref(false)
const showEdit = ref(false)
const showDelete = ref(false)
const areaEnEdicion = ref(null)
const areaAEliminar = ref(null)

// Handlers para acciones
function handleEdit(area) {
    areaEnEdicion.value = area
    showEdit.value = true
}
function handleDelete(area) {
    areaAEliminar.value = area
    showDelete.value = true
}

const { table, pageSizes, estadoSet, toggleEstado, clearEstado } =
    useAreasTable(props.data, props.filter, {
        onEdit: handleEdit,
        onDelete: handleDelete,
    })

function refreshAfterChange({ wasDelete = false } = {}) {
    const s = table.getState()

    // Si borraste y solo quedaba 1 fila visible, retrocede una página
    const shouldGoBack =
        wasDelete &&
        s.pagination.pageIndex > 0 &&
        table.getRowModel().rows.length === 1

    const nextPageIndex = shouldGoBack
        ? s.pagination.pageIndex - 1
        : s.pagination.pageIndex

    const filters = (s.columnFilters ?? []).reduce((acc, f) => {
        let v = f.value
        if (typeof v === 'string') v = v.trim()
        if (Array.isArray(v) ? v.length > 0 : v != null && v !== '') acc[f.id] = v
        return acc
    }, {})

    router.get(
        '/areas',
        {
            page: nextPageIndex + 1,
            per_page: s.pagination.pageSize,
            sort_field: s.sorting[0]?.id,
            sort_direction: s.sorting.length ? (s.sorting[0].desc ? 'desc' : 'asc') : undefined,
            ...filters,
        },
        { preserveState: false, preserveScroll: true, replace: true }
    )
}
</script>

<template>
    <div class="w-full">
        <Toolbar :table="table" :estadoSet="estadoSet" @toggleEstado="toggleEstado" @clearEstado="clearEstado"
            @create="showCreate = true" />

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
                            <!-- Eliminado ExpandedRow -->
                        </template>
                    </template>
                    <TableRow v-else>
                        <TableCell :colspan="table.getAllColumns().length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <Pagination :table="table" :pageSizes="pageSizes" />

        <!-- Modales -->
        <ConfirmDelete v-model="showDelete" :area="areaAEliminar"
            @deleted="() => refreshAfterChange({ wasDelete: true })" />
        <EditDialog v-model="showEdit" :area="areaEnEdicion" @updated="refreshAfterChange" />
        <CreateDialog v-model="showCreate" @created="refreshAfterChange" />
    </div>
</template>
