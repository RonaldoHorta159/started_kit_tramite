<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Spinner } from '@/components/ui/spinner'

const props = defineProps({
    modelValue: { type: Boolean, default: false },  // v-model:open
    area: { type: Object, default: null },
})
const emit = defineEmits(['update:modelValue', 'deleted'])

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const form = useForm({})

function doDelete() {
    if (!props.area?.id) return
    form.delete(`/areas/${props.area.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false
            emit('deleted')
        },
    })
}
</script>

<template>
    <AlertDialog v-model:open="open">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Eliminar área</AlertDialogTitle>
                <AlertDialogDescription>
                    Esta acción no se puede deshacer. Se eliminará el área
                    <strong>{{ area?.nombre }}</strong>.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel :disabled="form.processing">Cancelar</AlertDialogCancel>
                <AlertDialogAction :disabled="form.processing" @click="doDelete">
                    <template v-if="form.processing">
                        <Spinner class="mr-2 h-4 w-4" />
                        Eliminando…
                    </template>
                    <template v-else>Eliminar</template>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
