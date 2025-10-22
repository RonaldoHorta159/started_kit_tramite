<!-- resources/js/pages/areas/components/DataTable/Pagination.vue -->
<script setup>
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'

const { table, pageSizes } = defineProps({
    table: { type: Object, required: true },
    pageSizes: { type: Array, default: () => [5, 10, 15, 30, 50, 100] },
})
</script>

<template>
    <div class="flex items-center justify-end space-x-2 py-4">
        <div class="flex-1 text-sm text-muted-foreground">
            {{ table.getFilteredSelectedRowModel().rows.length }} de
            {{ table.getFilteredRowModel().rows.length }} filas seleccionadas.
        </div>

        <div class="flex items-center space-x-2">
            <p class="text-sm font-medium">Filas por página</p>
            <Select :model-value="table.getState().pagination.pageSize.toString()"
                @update:model-value="(value) => { table.setPageIndex(0); table.setPageSize(Number(value)) }">
                <SelectTrigger class="h-8 w-[70px]">
                    <SelectValue :placeholder="table.getState().pagination.pageSize.toString()" />
                </SelectTrigger>
                <SelectContent side="top">
                    <SelectItem v-for="n in pageSizes" :key="n" :value="n.toString()">{{ n }}</SelectItem>
                </SelectContent>
            </Select>
        </div>

        <div class="space-x-2">
            <div class="flex items-center space-x-2">
                <Button variant="outline" class="hidden h-8 w-8 p-0 lg:flex" :disabled="!table.getCanPreviousPage()"
                    @click="table.setPageIndex(0)">
                    <ChevronsLeft class="h-4 w-4" />
                </Button>
                <Button variant="outline" class="h-8 w-8 p-0" :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()">
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button variant="outline" class="h-8 w-8 p-0" :disabled="!table.getCanNextPage()"
                    @click="table.nextPage()">
                    <ChevronRight class="h-4 w-4" />
                </Button>
                <Button variant="outline" class="hidden h-8 w-8 p-0 lg:flex" :disabled="!table.getCanNextPage()"
                    @click="table.setPageIndex(table.getPageCount() - 1)">
                    <ChevronsRight class="h-4 w-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
