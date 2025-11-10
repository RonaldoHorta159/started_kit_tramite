// resources/js/pages/users/components/DataTable/useUsersTable.js
import { router } from '@inertiajs/vue3';
import {
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { computed, ref, toValue, watch } from 'vue';

export default function useUsersTable(
    dataRef, // 👈 PASA refs: toRef(props,'data')
    filterRef, // 👈 PASA refs: toRef(props,'filter')
    actions = {},
) {
    const _data = () => toValue(dataRef) || {};
    const _filter = () => toValue(filterRef) || {};

    // Helper para transformar el objeto de filtro al formato de array de TanStack
    const transformFilterObject = (filterObj) => {
        return Object.entries(filterObj)
            .filter(([, value]) => value !== null && value !== undefined && value !== '')
            .map(([id, value]) => ({ id, value }));
    };

    // estado base derivado de props reactivas
    const rowsRef = ref(_data().data ?? []);
    const lastPageRef = ref(_data().last_page ?? 0);
    const pageSizes = [5, 10, 15, 30, 50, 100];

    const sorting = ref([]);
    const columnFilters = ref(transformFilterObject(_filter()));
    const columnVisibility = ref({});
    const rowSelection = ref({});
    const expanded = ref({});
    const pagination = ref({
        pageIndex: Math.max(0, (_data().current_page ?? 1) - 1),
        pageSize: _data().per_page ?? 10,
    });

    function buildFilters() {
        return (columnFilters.value ?? []).reduce((acc, f) => {
            let v = f.value;
            if (typeof v === 'string') v = v.trim();
            if (Array.isArray(v) ? v.length > 0 : v != null && v !== '')
                acc[f.id] = v;
            return acc;
        }, {});
    }

    function sendQuery() {
        const s = {
            page: (pagination.value?.pageIndex ?? 0) + 1,
            per_page: pagination.value?.pageSize ?? 10,
            sort_field: sorting.value[0]?.id,
            sort_direction: sorting.value.length
                ? sorting.value[0].desc
                    ? 'desc'
                    : 'asc'
                : undefined,
            ...buildFilters(),
        };
        router.get('/users', s, { preserveState: false, preserveScroll: true });
    }

    const table = useVueTable({
        data: rowsRef.value,
        columns: (actions.createColumns ?? (() => []))(actions),
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getExpandedRowModel: getExpandedRowModel(),
        pageCount: lastPageRef.value,
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

        onColumnVisibilityChange: (v) =>
            (columnVisibility.value =
                typeof v === 'function' ? v(columnVisibility.value) : v),
        onRowSelectionChange: (v) =>
            (rowSelection.value =
                typeof v === 'function' ? v(rowSelection.value) : v),
        onExpandedChange: (v) =>
            (expanded.value = typeof v === 'function' ? v(expanded.value) : v),

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

    // 🔁 Reaccionar cuando Inertia re-hidrata `props.data` y `props.filter`
    watch(
        dataRef,
        (nv) => {
            const d = nv || {};
            rowsRef.value = d.data ?? [];
            lastPageRef.value = d.last_page ?? 0;
            // sincroniza paginación con servidor
            pagination.value = {
                pageIndex: Math.max(0, (d.current_page ?? 1) - 1),
                pageSize: d.per_page ?? pagination.value.pageSize,
            };
            // actualiza opciones de la tabla sin recrearla
            table.setOptions((opts) => ({
                ...opts,
                data: rowsRef.value,
                pageCount: lastPageRef.value,
            }));
        },
        { deep: true, immediate: true },
    );

    watch(
        filterRef,
        (nf) => {
            columnFilters.value = transformFilterObject(nf || {});
        },
        { deep: true, immediate: true },
    );

    // helper para refrescar desde fuera (modales)
    function refresh() {
        router.reload({ only: ['data', 'filter'] }); // rehidratación liviana
    }

    // Filtros multi (para Toolbar)
    const estadoSet = computed(
        () => new Set(table.getColumn('estado')?.getFilterValue() ?? []),
    );
    function toggleEstado(val, checked) {
        const set = new Set(estadoSet.value);
        checked ? set.add(val) : set.delete(val);
        table
            .getColumn('estado')
            ?.setFilterValue(set.size ? Array.from(set) : undefined);
    }
    function clearEstado() {
        table.getColumn('estado')?.setFilterValue(undefined);
    }

    const rolSet = computed(
        () => new Set(table.getColumn('rol')?.getFilterValue() ?? []),
    );
    function toggleRol(val, checked) {
        const set = new Set(rolSet.value);
        checked ? set.add(val) : set.delete(val);
        table
            .getColumn('rol')
            ?.setFilterValue(set.size ? Array.from(set) : undefined);
    }
    function clearRol() {
        table.getColumn('rol')?.setFilterValue(undefined);
    }

    function setAreaFilter(areaId) {
        table.getColumn('area_id')?.setFilterValue(areaId || undefined);
    }

    return {
        table,
        pageSizes,
        pagination,
        sorting,
        columnFilters,
        // filtros multi
        estadoSet,
        toggleEstado,
        clearEstado,
        rolSet,
        toggleRol,
        clearRol,
        // filtro select
        setAreaFilter,
        // refresh expuesto
        refresh,
    };
}
