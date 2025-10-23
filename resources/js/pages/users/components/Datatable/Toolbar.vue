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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    SelectGroup,        // 👈 faltaba
} from '@/components/ui/select'
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps({
    table: { type: Object, required: true },

    estadoSet: { type: Object, required: true },
    rolSet: { type: Object, required: true },

    areasOptions: { type: Array, default: () => [] },   // [{id,nombre}]
    rolesOptions: { type: Array, default: () => [] },   // ['Admin', ...]
    estadosOptions: { type: Array, default: () => [] }, // ['Activo','Inactivo']
})

const emit = defineEmits([
    'create',
    'toggleEstado', 'clearEstado',
    'toggleRol', 'clearRol',
    'setAreaFilter',
])

/* --------------------- Debounce para búsqueda global (q) --------------------- */
const qQuery = ref(props.table.getColumn('q')?.getFilterValue() ?? '')
let qTimer = null
const DEBOUNCE_MS = 400

function onQInput(val) {
    qQuery.value = val
    if (qTimer) clearTimeout(qTimer)
    qTimer = setTimeout(applyQNow, DEBOUNCE_MS)
}
function applyQNow() {
    if (qTimer) {
        clearTimeout(qTimer)
        qTimer = null
    }
    const v = (qQuery.value ?? '').trim()
    props.table.getColumn('q')?.setFilterValue(v || undefined)
}
onBeforeUnmount(() => { if (qTimer) clearTimeout(qTimer) })

/* ----------------------------- Filtro: Área (select) ----------------------------- */
// estado local para que el Select muestre la selección actual
const areaId = ref(null) // null | '*' | '123'
function onAreaSelect(value) {
    areaId.value = value ?? null
    // ⬇️ Si es "Todas" ('*'), no enviar filtro (undefined) → URL limpia
    emit('setAreaFilter', value === '*' ? undefined : (value || undefined))
}

/* ------------------------------- Helpers visuales ------------------------------- */
const estadoCount = () => props.estadoSet.size
const rolCount = () => props.rolSet.size

function onCreateClick() { emit('create') }
</script>

<template>
    <div class="flex gap-2 items-center justify-between py-4">
        <!-- Izquierda: filtros -->
        <div class="flex gap-2 items-center flex-wrap">
            <!-- Búsqueda global (dni/nombres/apellidos/email) -->
            <Input class="max-w-sm" placeholder="Buscar (DNI, nombre, email)…" v-model="qQuery"
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

            <!-- Filtro: Rol (multi) -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="h-8 border-dashed">
                        Rol
                        <Badge v-if="rolCount()" variant="secondary" class="ml-2">{{ rolCount() }}</Badge>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" class="w-[220px]">
                    <DropdownMenuCheckboxItem v-for="rol in rolesOptions" :key="rol" :checked="rolSet.has(rol)"
                        @select.prevent="$emit('toggleRol', rol, !rolSet.has(rol))">
                        {{ rol }}
                    </DropdownMenuCheckboxItem>

                    <DropdownMenuCheckboxItem v-if="rolCount()" :checked="false" class="justify-center"
                        @select.prevent="$emit('clearRol')">
                        Limpiar filtros
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- Filtro: Área (select simple por area_id) -->
            <Select :model-value="areaId" @update:model-value="val => onAreaSelect(val)">
                <SelectTrigger class="w-[220px]">
                    <SelectValue placeholder="Filtrar por área" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="*">Todas</SelectItem>
                    <SelectGroup>
                        <SelectItem v-for="a in areasOptions" :key="a.id" :value="String(a.id)">
                            {{ a.nombre }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
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
