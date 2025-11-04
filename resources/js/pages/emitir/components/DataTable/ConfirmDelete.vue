<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import { Button } from '@/components/ui/button'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'

// --- Props y Emits ---

const props = defineProps({
    open: Boolean,
    documento: { // El documento a eliminar
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['update:open', 'deleted'])

// --- Formulario de Eliminación ---

// Usamos useForm para manejar el estado 'processing' del borrado
const deleteForm = useForm({})

// --- Funciones ---

function submitDelete() {
    if (!props.documento) return

    // Llama a la ruta 'emitir.destroy' que definimos en web.php
    deleteForm.delete(route('emitir.destroy', props.documento.id), {
        onSuccess: () => {
            emit('deleted') // Avisa al padre que se borró
        },
        // No es necesario manejar onError, 'deleted' no se emitirá
    })
}

// Resetea el estado del formulario si se cierra el modal
watch(() => props.open, (newVal) => {
    if (!newVal) {
        deleteForm.reset()
    }
})

function closeModal() {
    emit('update:open', false)
}
</script>

<template>
    <AlertDialog :open="open" @update:open="closeModal">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>¿Estás realmente seguro?</AlertDialogTitle>
                <AlertDialogDescription>
                    Esta acción no se puede deshacer. Esto eliminará permanentemente el documento con N°:
                    <span class="font-medium">{{ documento?.nro_documento }}</span>
                    y asunto:
                    <span class="font-medium truncate">{{ documento?.asunto }}</span>.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="closeModal" :disabled="deleteForm.processing">
                    Cancelar
                </AlertDialogCancel>

                <AlertDialogAction as-child>
                    <Button variant="destructive" @click="submitDelete" :disabled="deleteForm.processing">
                        <span v-if="deleteForm.processing">Eliminando...</span>
                        <span v-else>Eliminar</span>
                    </Button>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
