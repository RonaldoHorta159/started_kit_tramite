<script setup>
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next'

// Recibimos la fila completa (el objeto 'documento') como prop
defineProps({
    row: Object,
})

// Definimos los eventos que este componente puede emitir
const emit = defineEmits(['edit', 'delete'])
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="h-8 w-8 p-0">
                <span class="sr-only">Abrir menú</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Acciones</DropdownMenuLabel>

            <DropdownMenuSeparator />

            <DropdownMenuItem @click="emit('edit', row)">
                <Pencil class="mr-2 h-4 w-4" />
                Editar
            </DropdownMenuItem>

            <DropdownMenuItem @click="emit('delete', row)" class="text-red-600 focus:text-red-600">
                <Trash2 class="mr-2 h-4 w-4" />
                Eliminar
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
