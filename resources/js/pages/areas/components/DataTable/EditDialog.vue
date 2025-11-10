<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'   // 👈 wrapper correcto
import { toast } from 'vue-sonner'

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    area: { type: Object, default: null },
})
const emit = defineEmits(['update:modelValue', 'updated'])

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const form = useForm({
    nombre: '',
    estado: '',
    codigo: '',
})

// precargar desde el prop (incluye estado)
watch(
    () => props.area,
    (a) => {
        if (!a) return
        form.nombre = a.nombre ?? ''
        form.estado = a.estado ?? ''   // 'Activo' | 'Inactivo'
        form.codigo = a.codigo ?? ''
    },
    { immediate: true }
)

// Booleano ⇄ string
const estadoBool = computed({
    get: () => form.estado === 'Activo',
    set: (v) => { form.estado = v ? 'Activo' : 'Inactivo' },
})

function submitForm() {
    if (!props.area?.id) return
    form.put(`/areas/${props.area.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`El área "${form.nombre}" ha sido actualizada ✅`)
            open.value = false
            emit('updated')
        },
    })
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Editar área</DialogTitle>
                <DialogDescription>Actualiza los datos del área seleccionada.</DialogDescription>
            </DialogHeader>

            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">Nombre</Label>
                    <Input id="name" v-model="form.nombre" class="col-span-3" :disabled="form.processing" />
                    <span v-if="form.errors?.nombre" class="col-span-4 text-red-600 text-sm">
                        {{ form.errors.nombre }}
                    </span>
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label class="text-right">Estado</Label>
                    <!-- ✅ v-model normal; si alguna vez ves desincronía, añade :key con area.id -->
                    <Switch v-model="estadoBool" :disabled="form.processing" />
                    <Label class="text-right">{{ form.estado || '—' }}</Label>
                </div>
            </div>

            <DialogFooter>
                <Button :disabled="form.processing" type="button" @click="submitForm">
                    <template v-if="form.processing">
                        <Spinner class="mr-2 h-4 w-4" /> Guardando…
                    </template>
                    <template v-else>Guardar cambios</template>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
