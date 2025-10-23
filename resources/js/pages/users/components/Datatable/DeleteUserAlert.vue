<!-- resources/js/pages/users/components/DataTable/DeleteUserAlert.vue -->
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
    modelValue: { type: Boolean, default: false },  // v-model:open
    user: { type: Object, default: null },
})
const emit = defineEmits(['update:modelValue', 'deleted'])

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const form = useForm({})

function doDelete() {
    if (!props.user?.id) return

    form.delete(`/users/${props.user.id}`, {
        preserveScroll: true,
        // asegura que Inertia no conserve estado previo
        preserveState: false,
        replace: true,
        onSuccess: () => {
            toast.success('Usuario eliminado correctamente')
            open.value = false
            emit('deleted')
            // 🔁 visita limpia a la misma ruta con los mismos query params
            router.get(`/users${window.location.search}`, {}, { preserveScroll: true, preserveState: false })
        },
        onError: () => {
            toast.error('Ocurrió un error al eliminar el usuario')
        },
    })
}
</script>

<template>
    <AlertDialog v-model:open="open">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Eliminar usuario</AlertDialogTitle>
            </AlertDialogHeader>

            <AlertDialogDescription>
                Esta acción no se puede deshacer. Se eliminará el usuario
                <strong v-if="user"> {{ user.nombres }} {{ user.apellido_paterno }} </strong>.
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
