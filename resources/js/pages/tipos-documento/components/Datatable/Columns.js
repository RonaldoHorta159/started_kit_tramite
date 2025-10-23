// resources/js/pages/tipos-documento/components/Datatable/Columns.js
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ArrowUpDown, MoreVertical, Pencil, Trash2 } from 'lucide-vue-next';
import { h } from 'vue';

export default function createColumns(actions = {}) {
    const { onEdit, onDelete } = actions;

    return [
        // Selección
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

        // Nombre
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
                h(
                    'div',
                    { class: 'font-medium' },
                    row.getValue('nombre') ?? '—',
                ),
        },

        // Estado
        {
            accessorKey: 'estado',
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
                    () => ['Estado', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
                ),
            cell: ({ row }) => {
                const estado = row.getValue('estado');
                const props = {};
                const label =
                    estado === 'Activo'
                        ? 'Activo'
                        : ((props.variant = 'outline'), 'Inactivo');
                return h(Badge, props, () => label);
            },
        },

        // Acciones
        {
            id: 'actions',
            enableHiding: false,
            cell: ({ row }) => {
                const item = row.original;
                return h('div', { class: 'flex items-center justify-end' }, [
                    h(DropdownMenu, null, {
                        default: () => [
                            h(DropdownMenuTrigger, { asChild: true }, () =>
                                h(
                                    Button,
                                    {
                                        variant: 'ghost',
                                        size: 'icon',
                                        class: 'h-8 w-8',
                                    },
                                    () => h(MoreVertical, { class: 'h-4 w-4' }),
                                ),
                            ),
                            h(DropdownMenuContent, { align: 'end' }, () => [
                                h(DropdownMenuLabel, null, () => 'Acciones'),
                                h(DropdownMenuSeparator),
                                h(
                                    DropdownMenuItem,
                                    { onSelect: () => onEdit && onEdit(item) },
                                    () => [
                                        h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                        'Editar',
                                    ],
                                ),
                                h(
                                    DropdownMenuItem,
                                    {
                                        class: 'text-red-600',
                                        onSelect: () =>
                                            onDelete && onDelete(item),
                                    },
                                    () => [
                                        h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                        'Eliminar',
                                    ],
                                ),
                            ]),
                        ],
                    }),
                ]);
            },
        },

        // filtros fantasma
        {
            id: 'q',
            header: () => null,
            cell: () => null,
            enableSorting: false,
            enableHiding: false,
        },
    ];
}
