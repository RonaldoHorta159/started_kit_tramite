<!-- resources/js/pages/areas/components/DataTable/RowActions.vue -->
<script setup>
import { Button } from '@/components/ui/button'
import { Eye } from 'lucide-vue-next'

const { row } = defineProps({
    row: { type: Object, required: true },
})
</script>

<template>
    <div class="flex items-center justify-end gap-1">
        <Button variant="ghost" size="sm" title="Ver detalle" @click="row.toggleExpanded()">
            <Eye class="h-4 w-4" />
        </Button>
    </div>
</template>
