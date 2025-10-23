<script setup>
import { computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
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
import { Spinner } from '@/components/ui/spinner'

const props = defineProps({
    modelValue: { type: Boolean, default: false }, // v-model:open
    item: { type: Object, default: null },         // { id, nombre, ... }
})
const emit = defineEmits(['update:modelValue', 'deleted'])

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const form = useForm({})

function doDelete() {
    if (!props.item?.id) return
    form.delete(`/tipos-documento/${props.item.id}`, {
        preserveScroll: true,
        preserveState: false,
        replace: true,
        onSuccess: () => {
            toast.success('Tipo de documento eliminado correctamente')
            open.value = false
            emit('deleted')
            router.get(`/tipos-documento${window.location.search}`, {}, { preserveScroll: true, preserveState: false })
        },
        onError: () => {
            toast.error('Ocurrió un error al eliminar el tipo de documento')
        },
    })
}
</script>

<template>
    <AlertDialog v-model:open="open">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Eliminar tipo de documento</AlertDialogTitle>
            </AlertDialogHeader>

            <AlertDialogDescription>
                Esta acción no se puede deshacer. Se eliminará el tipo de documento
                <strong v-if="item"> {{ item.nombre }} </strong>.
            </AlertDialogDescription>

            <AlertDialogFooter>
                <AlertDialogCancel :disabled="form.processing">Cancelar</AlertDialogCancel>
                <AlertDialogAction :disabled="form.processing" @click="doDelete">
                    <template v-if="form.processing">
                        <Spinner class="mr-2 h-4 w-4" /> Eliminando…
                    </template>
                    <template v-else>Eliminar</template>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
