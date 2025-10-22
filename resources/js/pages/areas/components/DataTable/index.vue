<script setup>
import { ref } from 'vue'
import { FlexRender } from '@tanstack/vue-table'

import useAreasTable from './useAreasTable'
import Toolbar from './Toolbar.vue'
import Pagination from './Pagination.vue'
import ExpandedRow from './ExpandedRow.vue'

import {
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table'

import CreateDialog from './CreateDialog.vue'

const props = defineProps({
    data: { type: Object, required: true },
    filter: { type: Array, default: () => [] },
})

const { table, pageSizes, estadoSet, toggleEstado, clearEstado } =
    useAreasTable(props.data, props.filter)

const showCreate = ref(false)
function onCreated() {
    // router.get('/areas', { per_page: table.getState().pagination.pageSize }, { preserveState: false, preserveScroll: true })
}
</script>


<template>
    <div class="w-full">
        <Toolbar :table="table" :estadoSet="estadoSet" @toggleEstado="toggleEstado" @clearEstado="clearEstado"
            @create="showCreate = true" />

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="hg in table.getHeaderGroups()" :key="hg.id">
                        <TableHead v-for="header in hg.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableRow :data-state="row.getIsSelected() && 'selected'">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                            <ExpandedRow v-if="row.getIsExpanded()" :row="row" />
                        </template>
                    </template>
                    <TableRow v-else>
                        <TableCell :colspan="table.getAllColumns().length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>



        <Pagination :table="table" :pageSizes="pageSizes" />

        <CreateDialog v-model="showCreate" @created="onCreated" />
    </div>
</template>
