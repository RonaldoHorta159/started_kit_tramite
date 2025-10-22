import { router } from '@inertiajs/vue3';
import {
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { computed, ref } from 'vue';
import createColumns from './Columns';

export default function useAreasTable(
    initialData,
    initialFilter = [],
    actions = {},
) {
    const rows = initialData.data;
    const pageSizes = [5, 10, 15, 30, 50, 100];

    const sorting = ref([]);
    const columnFilters = ref(initialFilter ?? []);
    const columnVisibility = ref({});
    const rowSelection = ref({});
    const expanded = ref({});
    const pagination = ref({
        pageIndex: initialData.current_page - 1,
        pageSize: initialData.per_page,
    });

    function sendQuery() {
        const filters = (columnFilters.value ?? []).reduce((acc, f) => {
            let v = f.value;
            if (typeof v === 'string') v = v.trim();
            if (Array.isArray(v) ? v.length > 0 : v !== '') acc[f.id] = v;
            return acc;
        }, {});

        router.get(
            '/areas',
            {
                page: (pagination.value?.pageIndex ?? 0) + 1,
                per_page: pagination.value?.pageSize ?? 10,
                sort_field: sorting.value[0]?.id,
                sort_direction: sorting.value.length
                    ? sorting.value[0].desc
                        ? 'desc'
                        : 'asc'
                    : undefined,
                ...filters,
            },
            { preserveState: false, preserveScroll: true },
        );
    }

    const table = useVueTable({
        data: rows,
        columns: createColumns(actions), // 👈 pasamos onEdit/onDelete
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getExpandedRowModel: getExpandedRowModel(),

        pageCount: initialData.last_page,
        manualPagination: true,
        manualSorting: true,
        manualFiltering: true,

        onPaginationChange: (updater) => {
            pagination.value =
                typeof updater === 'function'
                    ? updater(pagination.value)
                    : updater;
            sendQuery();
        },
        onSortingChange: (updaterOrValue) => {
            sorting.value =
                typeof updaterOrValue === 'function'
                    ? updaterOrValue(sorting.value)
                    : updaterOrValue;
            pagination.value = { ...pagination.value, pageIndex: 0 };
            sendQuery();
        },
        onColumnFiltersChange: (updaterOrValue) => {
            columnFilters.value =
                typeof updaterOrValue === 'function'
                    ? updaterOrValue(columnFilters.value)
                    : updaterOrValue;
            pagination.value = { ...pagination.value, pageIndex: 0 };
            sendQuery();
        },

        onColumnVisibilityChange: (updaterOrValue) =>
            (columnVisibility.value =
                typeof updaterOrValue === 'function'
                    ? updaterOrValue(columnVisibility.value)
                    : updaterOrValue),
        onRowSelectionChange: (updaterOrValue) =>
            (rowSelection.value =
                typeof updaterOrValue === 'function'
                    ? updaterOrValue(rowSelection.value)
                    : updaterOrValue),
        onExpandedChange: (updaterOrValue) =>
            (expanded.value =
                typeof updaterOrValue === 'function'
                    ? updaterOrValue(expanded.value)
                    : updaterOrValue),

        state: {
            get sorting() {
                return sorting.value;
            },
            get columnFilters() {
                return columnFilters.value;
            },
            get columnVisibility() {
                return columnVisibility.value;
            },
            get rowSelection() {
                return rowSelection.value;
            },
            get expanded() {
                return expanded.value;
            },
            get pagination() {
                return pagination.value;
            },
        },
    });

    // Filtro estado (se mantiene igual)
    const estadoSet = computed(() => {
        const values = table.getColumn('estado')?.getFilterValue() ?? [];
        return new Set(values);
    });
    function toggleEstado(val, checked) {
        const set = new Set(estadoSet.value);
        if (checked) set.add(val);
        else set.delete(val);
        const next = Array.from(set);
        const col = table.getColumn('estado');
        if (col) col.setFilterValue(next.length ? next : undefined);
    }
    function clearEstado() {
        const col = table.getColumn('estado');
        if (col) col.setFilterValue(undefined);
    }

    return {
        table,
        pageSizes,
        pagination,
        sorting,
        columnFilters,
        estadoSet,
        toggleEstado,
        clearEstado,
    };
}
