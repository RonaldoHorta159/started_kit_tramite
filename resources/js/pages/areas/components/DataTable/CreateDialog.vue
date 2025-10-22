<!-- resources/js/pages/areas/components/DataTable/CreateDialog.vue -->
<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import Switch from '@/components/ui/switch/Switch.vue'
import { toast } from 'vue-sonner'

const props = defineProps({
    modelValue: { type: Boolean, default: false }, // v-model:open
})
const emit = defineEmits(['update:modelValue', 'created'])

// v-model proxy sin watchers
const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const switchStatus = ref(false)
const form = useForm({
    nombreArea: '',
    estadoArea: '',
})

function SwitchActivate() {
    switchStatus.value = !switchStatus.value
    form.estadoArea = switchStatus.value ? 'ACTIVO' : 'INACTIVO'
}

function submitForm() {
    if (!form.nombreArea?.trim()) return // guard mínimo
    form.post('/areas', {
        onSuccess: () => {
            toast.success(`El área "${form.nombreArea}" ha sido creada exitosamente 🎉`, {
                description: 'Se registró correctamente en el sistema.',
                position: 'top-right',
            })
            // Cerrar y dejar limpio para el próximo uso
            open.value = false
            switchStatus.value = false
            form.reset()
            emit('created')
        },
        // onError: (errs) => { opcional: mostrar algo extra }
        // onFinish: () => { opcional: lógica después del request (éxito o error) }
    })
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Crear área</DialogTitle>
                <DialogDescription>Este modal registra el nombre de la nueva área.</DialogDescription>
            </DialogHeader>

            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">Nombre</Label>
                    <Input id="name" v-model="form.nombreArea" class="col-span-3" :disabled="form.processing" />
                    <span v-if="form.errors?.nombreArea" class="col-span-4 text-red-600 text-sm">
                        {{ form.errors.nombreArea }}
                    </span>
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="estado" class="text-right">Estado</Label>
                    <Switch id="estado" @click="SwitchActivate" :disabled="form.processing" />
                    <Label for="estado" class="text-right">
                        {{ form.estadoArea || '—' }}
                    </Label>
                </div>
            </div>

            <DialogFooter>
                <Button type="button" :disabled="form.processing || !form.nombreArea?.trim()" @click="submitForm">
                    <template v-if="form.processing">
                        <Spinner class="mr-2 h-4 w-4" />
                        Guardando…
                    </template>
                    <template v-else>
                        Crear
                    </template>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
