import { Badge } from '@/components/ui/badge'; // Importamos el componente Badge de shadcn-vue
import { h } from 'vue';
import RowActions from './RowActions.vue'; // Importamos las acciones de fila (que crearemos después)

// Definimos y exportamos las columnas para TanStack Table
export const columns = [
    // 1. Código Único
    {
        accessorKey: 'codigo_unico',
        header: 'Código Único',
    },
    // 2. N° Doc.
    {
        accessorKey: 'nro_documento',
        header: 'N° Doc.',
    },
    // 3. Fecha (usamos 'cell' para formatear)
    {
        accessorKey: 'created_at',
        header: 'Fecha',
        cell: ({ row }) => {
            // Formateamos la fecha (ej: 31/10/2025)
            const date = new Date(row.getValue('created_at'));
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            });
        },
    },
    // 4. Documento (usamos 'cell' para acceder a la relación)
    {
        accessorKey: 'tipo_documento',
        header: 'Documento',
        cell: ({ row }) => {
            // 'row.original' es el objeto completo de Laravel
            // Accedemos a la relación 'tipo_documento' que cargamos en el Controller
            const tipoDoc = row.original.tipo_documento;
            return h('span', tipoDoc?.nombre || 'N/A');
        },
    },
    // 5. Asunto (usamos 'cell' para truncar texto largo)
    {
        accessorKey: 'asunto',
        header: 'Asunto',
        cell: ({ row }) => {
            // Truncamos el texto si es muy largo para que no rompa la tabla
            return h(
                'span',
                { class: 'block w-64 truncate' },
                row.getValue('asunto'),
            );
        },
    },
    // 6. Nro Folios
    {
        accessorKey: 'folios',
        header: 'Nro Folios',
    },
    // 7. Destino (usamos 'cell' para acceder a la relación)
    {
        accessorKey: 'area_actual',
        header: 'Destino',
        cell: ({ row }) => {
            // Accedemos a la relación 'area_actual'
            const area = row.original.area_actual;
            return h('span', area?.nombre || 'N/A');
        },
    },
    // 8. Estado Destino (usamos 'cell' para mostrar un Badge)
    {
        accessorKey: 'estado',
        header: 'Estado Destino',
        cell: ({ row }) => {
            const estado = row.getValue('estado');

            // Asignamos un color al badge según el estado
            let variant = 'default';
            if (estado === 'Recibido') variant = 'secondary';
            if (estado === 'Archivado' || estado === 'Atendido')
                variant = 'success';
            if (estado === 'Rechazado') variant = 'destructive';

            return h(Badge, { variant: variant }, () => estado);
        },
    },
    // 9. Acciones (la columna final)
    {
        id: 'actions',
        header: 'Acciones',
        cell: ({ row }) => {
            // Renderizamos el componente RowActions, pasando la data de la fila
            return h(RowActions, { row: row.original });
        },
    },
];
