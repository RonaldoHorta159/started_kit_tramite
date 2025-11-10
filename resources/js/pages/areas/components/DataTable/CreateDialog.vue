<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'   // 👈 usa el wrapper de shadcn-vue
import { toast } from 'vue-sonner'

const props = defineProps({ modelValue: { type: Boolean, default: false } })
const emit = defineEmits(['update:modelValue', 'created'])

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const form = useForm({
    nombre: '',
    estado: 'Inactivo',  // default claro
})

// Booleano ⇄ string (Activo/Inactivo)
const estadoBool = computed({
    get: () => form.estado === 'Activo',
    set: (v) => { form.estado = v ? 'Activo' : 'Inactivo' },
})

function submitForm() {
    if (!form.nombre?.trim()) return
    form.post('/areas', {
        onSuccess: () => {
            toast.success(`El área "${form.nombre}" ha sido creada exitosamente 🎉`, {
                description: 'Se registró correctamente en el sistema.', position: 'top-right'
            })
            open.value = false
            form.reset()
            form.estado = 'Inactivo' // reset explícito
            emit('created')
        },
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
                    <Input id="name" v-model="form.nombre" class="col-span-3" :disabled="form.processing" />
                    <span v-if="form.errors?.nombre" class="col-span-4 text-red-600 text-sm">
                        {{ form.errors.nombre }}
                    </span>
                </div>

                <div class="grid grid-cols-4 items-center gap-4">
                    <Label class="text-right">Estado</Label>
                    <!-- ✅ v-model normal (usa modelValue/update:modelValue) -->
                    <Switch v-model="estadoBool" :disabled="form.processing" />
                    <Label class="text-right">{{ form.estado }}</Label>
                </div>
            </div>

            <DialogFooter>
                <Button type="button" :disabled="form.processing || !form.nombre?.trim()" @click="submitForm">
                    <template v-if="form.processing">
                        <Spinner class="mr-2 h-4 w-4" /> Guardando…
                    </template>
                    <template v-else>Crear</template>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
