// resources/js/pages/users/components/DataTable/Columns.js
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
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

        // Usuario (avatar + nombre)
        {
            id: 'nombres',
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
                    () => [
                        'Usuario',
                        h(ArrowUpDown, { class: 'ml-2 h-4 w-4' }),
                    ],
                ),
            cell: ({ row }) => {
                const u = row.original;
                const fullName =
                    `${u.nombres ?? ''} ${u.apellido_paterno ?? ''} ${u.apellido_materno ?? ''}`.trim();
                const initials =
                    (u.nombres?.[0] ?? '').toUpperCase() +
                    (u.apellido_paterno?.[0] ?? '').toUpperCase();

                return h('div', { class: 'flex items-center gap-3' }, [
                    h(
                        Avatar,
                        { class: 'h-9 w-9' },
                        {
                            default: () => [
                                h(AvatarImage, {
                                    src: u.foto_path || '',
                                    alt: fullName || 'Usuario',
                                }),
                                h(AvatarFallback, null, () => initials || 'U'),
                            ],
                        },
                    ),
                    h('div', { class: 'flex flex-col' }, [
                        h(
                            'span',
                            { class: 'font-medium leading-none' },
                            fullName || '—',
                        ),
                    ]),
                ]);
            },
        },

        // DNI
        {
            accessorKey: 'dni',
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
                    () => ['DNI', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
                ),
            cell: ({ row }) =>
                h('div', { class: 'lowercase' }, row.getValue('dni') ?? '—'),
        },

        // Área (sin orden para evitar inconsistencias con backend)
        {
            id: 'area',
            accessorFn: (row) =>
                row.primary_area?.nombre ?? row.primaryArea?.nombre ?? null,
            header: () => 'Área',
            cell: ({ row }) => h('div', null, row.getValue('area') ?? '—'),
            enableSorting: false,
        },

        // Rol
        {
            accessorKey: 'rol',
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
                    () => ['Rol', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
                ),
            cell: ({ row }) => {
                const rol = row.getValue('rol') ?? '—';
                return h(Badge, { variant: 'outline' }, () => rol);
            },
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
                const user = row.original;
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
                                    { onSelect: () => onEdit && onEdit(user) },
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
                                            onDelete && onDelete(user),
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

        // filtros “fantasma”
        {
            id: 'q',
            header: () => null,
            cell: () => null,
            enableSorting: false,
            enableHiding: false,
        },
        {
            id: 'area_id',
            header: () => null,
            cell: () => null,
            enableSorting: false,
            enableHiding: false,
        },
    ];
}
