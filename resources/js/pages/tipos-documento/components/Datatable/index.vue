<script setup>
import { FlexRender } from '@tanstack/vue-table'
import { ref, toRef } from 'vue'
import useTiposDocumentoTable from './useTiposDocumentoTable'
import createColumns from './Columns'
import Toolbar from './Toolbar.vue'
import Pagination from './Pagination.vue'
import TipoDocumentoFormModal from './TipoDocumentoFormModal.vue'
import DeleteTipoDocumentoAlert from './DeleteTipoDocumentoAlert.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const props = defineProps({
    data: { type: Object, required: true },
    filter: { type: Array, default: () => [] },
    estadosOptions: { type: Array, default: () => [] },
})

const showForm = ref(false)
const itemToEdit = ref(null)
const showDeleteAlert = ref(false)
const itemToDelete = ref(null)

function handleEdit(item) { itemToEdit.value = item; showForm.value = true }
function handleDelete(item) { itemToDelete.value = item; showDeleteAlert.value = true }
function handleCreate() { itemToEdit.value = null; showForm.value = true }

const {
    table, pageSizes,
    estadoSet, toggleEstado, clearEstado,
    refresh,
} = useTiposDocumentoTable(toRef(props, 'data'), toRef(props, 'filter'), {
    createColumns,
    onEdit: handleEdit,
    onDelete: handleDelete,
})

function onSaved() { refresh() }
function onDeleted() { refresh() }
</script>

<template>
    <div class="w-full">
        <Toolbar :table="table" :estadoSet="estadoSet" :estados-options="estadosOptions" @toggleEstado="toggleEstado"
            @clearEstado="clearEstado" @create="handleCreate" />

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
                        <TableCell :colspan="table.getAllColumns().length" class="h-24 text-center">No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <Pagination :page="table.getState().pagination.pageIndex + 1" :pageSize="table.getState().pagination.pageSize"
            :total="data.total ?? 0" :pageSizeOptions="pageSizes" @update:page="(p) => table.setPageIndex(p - 1)"
            @update:pageSize="(s) => table.setPageSize(s)" />

        <TipoDocumentoFormModal v-model:open="showForm" :item="itemToEdit" :estados-options="estadosOptions"
            @update:open="val => { if (!val) itemToEdit.value = null }" @saved="onSaved" />

        <DeleteTipoDocumentoAlert v-model:open="showDeleteAlert" :item="itemToDelete"
            @update:modelValue="val => { if (!val) itemToDelete.value = null }" @deleted="onDeleted" />
    </div>
</template>
