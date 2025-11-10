<script setup>
import { FlexRender } from '@tanstack/vue-table'
import { ref, toRef } from 'vue'
import useUsersTable from './useUsersTable'
import createColumns from './Columns'
import Toolbar from './Toolbar.vue'
import Pagination from './Pagination.vue'
import UserFormModal from './UserFormModal.vue'
import DeleteUserAlert from './DeleteUserAlert.vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const props = defineProps({
    data: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    areasOptions: { type: Array, default: () => [] },
    rolesOptions: { type: Array, default: () => [] },
    estadosOptions: { type: Array, default: () => [] },
})

const showUserForm = ref(false)
const userToEdit = ref(null)
const showDeleteAlert = ref(false)
const userToDelete = ref(null)

function handleEdit(user) { userToEdit.value = user; showUserForm.value = true }
function handleDelete(user) { userToDelete.value = user; showDeleteAlert.value = true }
function handleCreate() { userToEdit.value = null; showUserForm.value = true }

// 👇 PASA refs de props
const {
    table, pageSizes,
    estadoSet, toggleEstado, clearEstado,
    rolSet, toggleRol, clearRol,
    setAreaFilter,
    refresh, // 👈 lo usaremos tras crear/editar/eliminar
} = useUsersTable(toRef(props, 'data'), toRef(props, 'filter'), {
    createColumns,
    onEdit: handleEdit,
    onDelete: handleDelete,
})

// hooks desde los modales
function onSaved() { refresh() }        // tras crear/editar
function onDeleted() { refresh() }        // tras borrar
</script>

<template>
    <div class="w-full">
        <Toolbar :table="table" :estadoSet="estadoSet" :rolSet="rolSet" :areas-options="areasOptions"
            :roles-options="rolesOptions" :estados-options="estadosOptions" @toggleEstado="toggleEstado"
            @clearEstado="clearEstado" @toggleRol="toggleRol" @clearRol="clearRol" @setAreaFilter="setAreaFilter"
            @create="handleCreate" />

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

        <UserFormModal v-model:open="showUserForm" :user="userToEdit" :areas-options="areasOptions"
            :roles-options="rolesOptions" :estados-options="estadosOptions"
            @update:open="val => { if (!val) userToEdit.value = null }" @saved="onSaved" />

        <DeleteUserAlert v-model:open="showDeleteAlert" :user="userToDelete"
            @update:modelValue="val => { if (!val) userToDelete.value = null }" @deleted="onDeleted" />
    </div>
</template>
