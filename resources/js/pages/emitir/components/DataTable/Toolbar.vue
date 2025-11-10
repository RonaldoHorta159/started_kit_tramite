<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import * as emitir from '@/routes/emitir'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { PlusCircle } from 'lucide-vue-next'

// Recibimos los filtros actuales (ej. la búsqueda) desde el controlador
const props = defineProps({
    filters: Object,
})

// Definimos el evento 'create' que se emitirá al hacer clic en el botón
const emit = defineEmits(['create'])

// Creamos una referencia reactiva para el campo de búsqueda
const search = ref(props.filters.search)

// Usamos 'watch' y 'debounce' para la búsqueda (mejora de rendimiento)
// Esto espera 300ms después de que el usuario deja de escribir para enviar la petición.
watch(search, debounce((value) => {
    router.get(
        emitir.index(), // Llama a la ruta 'emitir.index'
        { search: value },   // Pasa el valor de búsqueda como query param
        {
            preserveState: true, // Mantiene el estado de la página (no recarga)
            replace: true,       // Reemplaza la entrada en el historial del navegador
        }
    )
}, 300))
</script>

<template>
    <div class="flex items-center justify-between py-4">
        <div class="flex items-center space-x-2">
            <Input v-model="search" type="text" placeholder="Buscar por código, N° doc o asunto..." class="max-w-sm" />
        </div>

        <Button @click="emit('create')">
            <PlusCircle class="mr-2 h-4 w-4" />
            Emitir Documento
        </Button>
    </div>
</template>
