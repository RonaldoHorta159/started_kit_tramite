<script setup>
import { computed } from 'vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { ListFilter, Search } from 'lucide-vue-next'

const props = defineProps({
    table: { type: Object, required: true },
    estadoSet: { type: Set, required: true },
    estadosOptions: { type: Array, default: () => [] },
})

const emit = defineEmits(['toggleEstado', 'clearEstado'])

const q = computed({
    get: () => props.table.getColumn('q')?.getFilterValue() ?? '',
    set: val => props.table.getColumn('q')?.setFilterValue(val),
})
</script>

<template>
    <div class="flex items-center justify-between py-4">
        <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input v-model="q" placeholder="Buscar por N° Documento o Asunto..." class="pl-10 w-full md:w-1/2 lg:w-1/3" />
        </div>

        <div class="flex items-center gap-2">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        <ListFilter class="mr-2 h-4 w-4" />
                        Estado
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Filtrar por estado</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuCheckboxItem v-for="option in estadosOptions" :key="option"
                        :checked="estadoSet.has(option)" @update:checked="(checked) => emit('toggleEstado', option, checked)">
                        {{ option }}
                    </DropdownMenuCheckboxItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem @click="emit('clearEstado')">
                        Limpiar filtros
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
