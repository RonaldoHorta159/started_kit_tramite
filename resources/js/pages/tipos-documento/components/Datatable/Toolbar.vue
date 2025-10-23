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

const props = defineProps({
    table: { type: Object, required: true },
    estadoSet: { type: Object, required: true },
    estadosOptions: { type: Array, default: () => [] },
})

const emit = defineEmits([
    'create',
    'toggleEstado', 'clearEstado',
])

/* ---- Debounce búsqueda global (q) ---- */
const qQuery = ref(props.table.getColumn('q')?.getFilterValue() ?? '')
let qTimer = null
const DEBOUNCE_MS = 400

function onQInput(val) {
    qQuery.value = val
    if (qTimer) clearTimeout(qTimer)
    qTimer = setTimeout(applyQNow, DEBOUNCE_MS)
}
function applyQNow() {
    if (qTimer) { clearTimeout(qTimer); qTimer = null }
    const v = (qQuery.value ?? '').trim()
    props.table.getColumn('q')?.setFilterValue(v || undefined)
}
onBeforeUnmount(() => { if (qTimer) clearTimeout(qTimer) })

/* ---- Helpers ---- */
const estadoCount = () => props.estadoSet.size
function onCreateClick() { emit('create') }
</script>

<template>
    <div class="flex gap-2 items-center justify-between py-4">
        <!-- Izquierda: filtros -->
        <div class="flex gap-2 items-center flex-wrap">
            <!-- Búsqueda -->
            <Input class="max-w-sm" placeholder="Buscar por nombre…" v-model="qQuery"
                @input="onQInput($event?.target?.value)" @keyup.enter="applyQNow" @blur="applyQNow" />

            <!-- Filtro: Estado (multi) -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="h-8 border-dashed">
                        Estado
                        <Badge v-if="estadoCount()" variant="secondary" class="ml-2">{{ estadoCount() }}</Badge>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" class="w-[220px]">
                    <DropdownMenuCheckboxItem v-for="est in estadosOptions" :key="est" :checked="estadoSet.has(est)"
                        @select.prevent="$emit('toggleEstado', est, !estadoSet.has(est))">
                        {{ est }}
                    </DropdownMenuCheckboxItem>

                    <DropdownMenuCheckboxItem v-if="estadoCount()" :checked="false" class="justify-center"
                        @select.prevent="$emit('clearEstado')">
                        Limpiar filtros
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <!-- Derecha: crear + columnas -->
        <div class="flex gap-2 items-center">
            <Button variant="outline" @click="onCreateClick">Crear nuevo</Button>

            <!-- Menú Columnas -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Columnas
                        <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuCheckboxItem v-for="column in props.table.getAllColumns().filter(c => c.getCanHide())"
                        :key="column.id" class="capitalize" :model-value="column.getIsVisible()"
                        @update:model-value="value => column.toggleVisibility(!!value)">
                        {{ column.id }}
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
