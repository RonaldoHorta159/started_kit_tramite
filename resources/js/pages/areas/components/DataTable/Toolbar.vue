<!-- resources/js/pages/areas/components/DataTable/Toolbar.vue -->
<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { ChevronDown } from 'lucide-vue-next'

const { table, estadoSet } = defineProps({
    table: { type: Object, required: true },
    estadoSet: { type: Object, required: true }, // Set(computed)
})
const emit = defineEmits(['create', 'toggleEstado', 'clearEstado'])

// --- Debounce para "nombre" ---
const nombreQuery = ref(table.getColumn('nombre')?.getFilterValue() ?? '')
let nombreTimer = null
const DEBOUNCE_MS = 400

function onNombreInput(val) {
    nombreQuery.value = val
    if (nombreTimer) clearTimeout(nombreTimer)
    nombreTimer = setTimeout(applyNombreNow, DEBOUNCE_MS)
}

function applyNombreNow() {
    if (nombreTimer) {
        clearTimeout(nombreTimer)
        nombreTimer = null
    }
    const v = (nombreQuery.value ?? '').trim()
    table.getColumn('nombre')?.setFilterValue(v)
}

onBeforeUnmount(() => {
    if (nombreTimer) clearTimeout(nombreTimer)
})

// Acciones
function onCreate() { emit('create') }
function onToggleActivo() { emit('toggleEstado', 'ACTIVO', !estadoSet.has('ACTIVO')) }
function onToggleInactivo() { emit('toggleEstado', 'INACTIVO', !estadoSet.has('INACTIVO')) }
function onClear() { emit('clearEstado') }
</script>


<template>
    <div class="flex gap-2 items-center justify-between py-4">
        <!-- Izquierda: filtros -->
        <div class="flex gap-2 items-center">
            <Input class="max-w-sm" placeholder="Filtrar por nombre..." v-model="nombreQuery"
                @input="onNombreInput($event?.target?.value)" @keyup.enter="applyNombreNow" @blur="applyNombreNow"
                @keydown.esc="() => { nombreQuery = ''; applyNombreNow() }" />
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="h-8 border-dashed">
                        Estado
                        <Badge v-if="estadoSet.size" variant="secondary" class="ml-2">{{ estadoSet.size }}</Badge>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" class="w-[220px]">
                    <DropdownMenuCheckboxItem :checked="estadoSet.has('ACTIVO')" @select.prevent="onToggleActivo">
                        Activo
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem :checked="estadoSet.has('INACTIVO')" @select.prevent="onToggleInactivo">
                        Inactivo
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuCheckboxItem v-if="estadoSet.size" :checked="false" class="justify-center"
                        @select.prevent="onClear">
                        Limpiar filtros
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <!-- Derecha: acciones -->
        <div class="flex gap-2 items-center">
            <Button variant="outline" @click="onCreate">Crear nuevo</Button>

            <!-- 👇 Columnas: ahora aquí al lado del botón -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Columnas
                        <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuCheckboxItem v-for="column in table.getAllColumns().filter(c => c.getCanHide())"
                        :key="column.id" class="capitalize" :model-value="column.getIsVisible()"
                        @update:model-value="(value) => column.toggleVisibility(!!value)">
                        {{ column.id }}
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
