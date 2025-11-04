<script setup>
import { ref, computed } from 'vue'
import {
    FlexRender,
} from '@tanstack/vue-table'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableEmpty,
} from '@/components/ui/table'

// 1. Importar todas las piezas que creamos
import { columns } from './Columns'
import { useEmitirTable } from './useEmitirTable'
import Toolbar from './Toolbar.vue'
import Pagination from './Pagination.vue'
import CreateDialog from './CreateDialog.vue'
import EditDialog from './EditDialog.vue'
import ConfirmDelete from './ConfirmDelete.vue'

// --- Props ---
// Recibimos los datos del controlador (pasados desde la página principal)
const props = defineProps({
    documentos: Object, // El objeto de paginación de Laravel
    filters: Object,    // Los filtros de búsqueda

    // Estos son necesarios para los modales de Crear/Editar
    // Tendremos que agregarlos al controlador
    areas: Array,
    tiposDocumento: Array,
})

// --- Estado de los Modales ---
const isCreateOpen = ref(false)
const isEditOpen = ref(false)
const isDeleteOpen = ref(false)

// Guarda el documento que se seleccionó para editar o borrar
const selectedDocumento = ref(null)

// --- Lógica de la Tabla ---
// Creamos una referencia 'computed' a los datos para la reactividad
const data = computed(() => props.documentos.data)

// Inicializamos la tabla usando nuestro composable
const { table } = useEmitirTable(data, columns)

// --- Manejadores de Eventos ---

// Abre el modal de CREAR
function handleCreate() {
    isCreateOpen.value = true
}

// Abre el modal de EDITAR (recibe el documento desde RowActions)
function handleEdit(documento) {
    selectedDocumento.value = documento
    isEditOpen.value = true
}

// Abre el modal de ELIMINAR (recibe el documento desde RowActions)
function handleDelete(documento) {
    selectedDocumento.value = documento
    isDeleteOpen.value = true
}

// Cierra el modal de CREAR (llamado por el evento @created)
function onCreated() {
    isCreateOpen.value = false
    // Opcional: mostrar un toast de éxito
}

// Cierra el modal de EDITAR (llamado por el evento @updated)
function onUpdated() {
    isEditOpen.value = false
    selectedDocumento.value = null
    // Opcional: mostrar un toast de éxito
}

// Cierra el modal de ELIMINAR (llamado por el evento @deleted)
function onDeleted() {
    isDeleteOpen.value = false
    selectedDocumento.value = null
    // Opcional: mostrar un toast de éxito
}
</script>

<template>
    <div class="space-y-4">
        <Toolbar :filters="filters" @create="handleCreate" />

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="table.getRowModel().rows.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()"
                                    @edit="handleEdit(row.original)" @delete="handleDelete(row.original)" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableEmpty v-else :colspan="columns.length">
                        No se encontraron resultados.
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <Pagination :links="documentos.links" :from="documentos.from" :to="documentos.to" :total="documentos.total" />
    </div>

    <CreateDialog v-model:open="isCreateOpen" :areas="props.areas" :tipos-documento="props.tiposDocumento"
        @created="onCreated" />

    <EditDialog v-model:open="isEditOpen" :documento="selectedDocumento" :areas="props.areas"
        :tipos-documento="props.tiposDocumento" @updated="onUpdated" />

    <ConfirmDelete v-model:open="isDeleteOpen" :documento="selectedDocumento" @deleted="onDeleted" />
</template>
