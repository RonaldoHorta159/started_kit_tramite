<!-- resources/js/pages/areas/components/DataTable/ExpandedRow.vue -->
<script setup>
const { row } = defineProps({
    row: { type: Object, required: true },
})
</script>

<template>
    <tr>
        <td :colspan="row.getAllCells().length" class="bg-muted/40">
            <div class="grid grid-cols-3 gap-2 text-sm">
                <div><span class="font-medium">ID:</span> {{ row.original.id }}</div>
                <div><span class="font-medium">Nombre:</span> {{ row.original.nombre }}</div>
                <div><span class="font-medium">Código:</span> {{ row.original.codigo ?? '—' }}</div>
                <div><span class="font-medium">Estado:</span> {{ row.original.estado }}</div>
                <div><span class="font-medium">Creado:</span> {{ row.original.created_at }}</div>
                <div><span class="font-medium">Actualizado:</span> {{ row.original.updated_at }}</div>
            </div>
        </td>
    </tr>
</template>
