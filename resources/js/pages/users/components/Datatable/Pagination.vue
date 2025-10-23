<script setup>
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import {
    Select,
    SelectTrigger,
    SelectContent,
    SelectItem,
    SelectValue,
    SelectGroup,
} from '@/components/ui/select'
import { Label } from '@/components/ui/label'
import { ChevronsLeft, ChevronLeft, ChevronRight, ChevronsRight } from 'lucide-vue-next'

const props = defineProps({
    page: { type: Number, default: 1 },              // página actual (1-based)
    pageSize: { type: Number, default: 10 },         // items por página
    total: { type: Number, default: 0 },             // total de registros
    pageSizeOptions: { type: Array, default: () => [10, 25, 50, 100] }
})

const emit = defineEmits(['update:page', 'update:pageSize'])

const totalPages = computed(() => Math.max(1, Math.ceil(props.total / props.pageSize)))
const canPrev = computed(() => props.page > 1)
const canNext = computed(() => props.page < totalPages.value)

function goFirst() { if (canPrev.value) emit('update:page', 1) }
function goPrev() { if (canPrev.value) emit('update:page', props.page - 1) }
function goNext() { if (canNext.value) emit('update:page', props.page + 1) }
function goLast() { if (canNext.value) emit('update:page', totalPages.value) }

// Select emite string: convierte a número y resetea a página 1
function onPageSizeChange(val) {
    const newSize = Number(val)
    if (!Number.isNaN(newSize) && newSize > 0) {
        emit('update:pageSize', newSize)
        emit('update:page', 1)
    }
}
</script>

<template>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
        <!-- Resumen -->
        <div class="text-sm text-muted-foreground">
            <span v-if="total > 0">
                Mostrando
                <strong class="text-foreground">{{ (page - 1) * pageSize + 1 }}</strong>
                –
                <strong class="text-foreground">{{ Math.min(page * pageSize, total) }}</strong>
                de
                <strong class="text-foreground">{{ total }}</strong>
            </span>
            <span v-else>No hay resultados</span>
        </div>

        <!-- Controles -->
        <div class="flex items-center gap-3">
            <!-- Tamaño de página -->
            <div class="flex items-center gap-2">
                <Label class="text-sm">Filas</Label>
                <Select :model-value="String(pageSize)" @update:model-value="onPageSizeChange">
                    <SelectTrigger class="w-[90px] h-9">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem v-for="opt in pageSizeOptions" :key="opt" :value="String(opt)">
                                {{ opt }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>

            <!-- Navegación -->
            <div class="flex items-center gap-1">
                <Button variant="outline" size="sm" :disabled="!canPrev" @click="goFirst" class="h-9 w-9 p-0">
                    <ChevronsLeft class="h-4 w-4" />
                </Button>
                <Button variant="outline" size="sm" :disabled="!canPrev" @click="goPrev" class="h-9 w-9 p-0">
                    <ChevronLeft class="h-4 w-4" />
                </Button>

                <span class="px-2 text-sm tabular-nums">
                    Página <strong>{{ page }}</strong> / <strong>{{ totalPages }}</strong>
                </span>

                <Button variant="outline" size="sm" :disabled="!canNext" @click="goNext" class="h-9 w-9 p-0">
                    <ChevronRight class="h-4 w-4" />
                </Button>
                <Button variant="outline" size="sm" :disabled="!canNext" @click="goLast" class="h-9 w-9 p-0">
                    <ChevronsRight class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
