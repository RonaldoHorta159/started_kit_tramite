import { h } from 'vue'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { ArrowUpDown, MoreHorizontal } from 'lucide-vue-next'
import { Checkbox } from '@/components/ui/checkbox'

export default function createColumns({ onDerivar, onArchivar, onVerTrazabilidad }) {
    return [
        {
            id: 'select',
            header: ({ table }) => h(Checkbox, {
                'checked': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
                'onUpdate:checked': value => table.toggleAllPageRowsSelected(!!value),
                'aria-label': 'Select all',
            }),
            cell: ({ row }) => h(Checkbox, {
                'checked': row.getIsSelected(),
                'onUpdate:checked': value => row.toggleSelected(!!value),
                'aria-label': 'Select row',
            }),
            enableSorting: false,
            enableHiding: false,
        },
        {
            accessorKey: 'nro_documento',
            header: ({ column }) => {
                return h(Button, {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                }, () => ['N° Documento', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
            },
            cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('nro_documento')),
        },
        {
            accessorKey: 'asunto',
            header: 'Asunto',
            cell: ({ row }) => h('div', { class: 'text-left' }, row.getValue('asunto')),
        },
        {
            accessorKey: 'areaOrigen',
            header: 'Área Origen',
            cell: ({ row }) => h('div', {}, row.original.area_origen?.nombre || 'N/A'),
        },
        {
            accessorKey: 'tipoDocumento',
            header: 'Tipo',
            cell: ({ row }) => h('div', {}, row.original.tipo_documento?.nombre || 'N/A'),
        },
        {
            accessorKey: 'estado',
            header: 'Estado',
            cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('estado')),
        },
        {
            accessorKey: 'created_at',
            header: 'Fecha de Envío',
            cell: ({ row }) => h('div', {}, new Date(row.getValue('created_at')).toLocaleDateString()),
        },
        {
            id: 'actions',
            enableHiding: false,
            cell: ({ row }) => {
                const doc = row.original
                return h('div', { class: 'relative' }, h(DropdownMenu, {}, () => [
                    h(DropdownMenuTrigger, { asChild: true }, () => h(Button, { variant: 'ghost', class: 'h-8 w-8 p-0' }, () => [
                        h('span', { class: 'sr-only' }, 'Open menu'),
                        h(MoreHorizontal, { class: 'h-4 w-4' }),
                    ])),
                    h(DropdownMenuContent, { align: 'end' }, () => [
                        h(DropdownMenuLabel, {}, 'Acciones'),
                        h(DropdownMenuItem, { onClick: () => onVerTrazabilidad(doc) }, 'Ver Trazabilidad'),
                        h(DropdownMenuSeparator),
                        h(DropdownMenuItem, { onClick: () => onDerivar(doc) }, 'Derivar'),
                        h(DropdownMenuItem, { onClick: () => onArchivar(doc) }, 'Archivar'),
                    ]),
                ]))
            },
        },
    ]
}
