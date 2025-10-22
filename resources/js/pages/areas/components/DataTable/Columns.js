// resources/js/pages/areas/components/DataTable/Columns.js
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowUpDown, Eye } from 'lucide-vue-next';
import { h } from 'vue';

export default function createColumns() {
    return [
        {
            id: 'select',
            header: ({ table }) =>
                h(Checkbox, {
                    modelValue:
                        table.getIsAllPageRowsSelected() ||
                        (table.getIsSomePageRowsSelected() && 'indeterminate'),
                    'onUpdate:modelValue': (v) =>
                        table.toggleAllPageRowsSelected(!!v),
                    ariaLabel: 'Select all',
                }),
            cell: ({ row }) =>
                h(Checkbox, {
                    modelValue: row.getIsSelected(),
                    'onUpdate:modelValue': (v) => row.toggleSelected(!!v),
                    ariaLabel: 'Select row',
                }),
            enableSorting: false,
            enableHiding: false,
        },
        {
            accessorKey: 'nombre',
            header: ({ column }) =>
                h(
                    Button,
                    {
                        variant: 'ghost',
                        onClick: () =>
                            column.toggleSorting(
                                column.getIsSorted() === 'asc',
                            ),
                    },
                    () => ['Nombre', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
                ),
            cell: ({ row }) =>
                h('div', { class: 'lowercase' }, row.getValue('nombre')),
        },
        {
            accessorKey: 'codigo',
            header: ({ column }) =>
                h(
                    Button,
                    {
                        variant: 'ghost',
                        onClick: () =>
                            column.toggleSorting(
                                column.getIsSorted() === 'asc',
                            ),
                    },
                    () => ['Código', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
                ),
            cell: ({ row }) => {
                const val = row.getValue('codigo');
                return h('div', { class: 'lowercase' }, val ?? '—');
            },
        },
        {
            accessorKey: 'estado',
            header: 'Estado',
            cell: ({ row }) => {
                const estado = row.getValue('estado');
                const props = {};
                const texto =
                    estado === 'ACTIVO'
                        ? 'Activo'
                        : ((props.variant = 'outline'), 'Inactivo');
                return h(Badge, props, () => texto);
            },
        },
        {
            id: 'actions',
            enableHiding: false,
            cell: ({ row }) =>
                h('div', { class: 'flex items-center justify-end gap-1' }, [
                    h(
                        Button,
                        {
                            variant: 'ghost',
                            size: 'sm',
                            onClick: () => row.toggleExpanded(),
                            title: 'Ver detalle',
                        },
                        () => h(Eye, { class: 'h-4 w-4' }),
                    ),
                ]),
        },
    ];
}
