import { getCoreRowModel, useVueTable } from '@tanstack/vue-table';

/**
 * Composable para gestionar la instancia de TanStack Table.
 *
 * @param {import('vue').ComputedRef<Array>} data - Una referencia computada a los datos (ej. props.documentos.data)
 * @param {Array} columns - La definición de columnas (de Columns.js)
 */
export function useEmitirTable(data, columns) {
    // Inicializamos la tabla
    const table = useVueTable({
        // Usamos un 'getter' para que sea reactivo a los cambios en los props
        get data() {
            return data.value;
        },
        columns,

        // Función principal para obtener el modelo de filas
        getCoreRowModel: getCoreRowModel(),

        // --- Configuración Server-Side ---
        // Le decimos a la tabla que no intente paginar o filtrar
        // por sí misma, ya que eso lo hace Laravel.
        manualPagination: true,
        manualFiltering: true,
        manualSorting: true, // Asumimos que el orden tmb lo da el servidor
    });

    // Devolvemos la instancia de la tabla para usarla en el componente
    return {
        table,
    };
}
