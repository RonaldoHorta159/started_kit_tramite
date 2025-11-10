<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import InputError from '@/components/InputError.vue'

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    documento: { type: Object, default: null },
    areasOptions: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue', 'submitted'])

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const form = useForm({
    area_destino_id: null,
    proveido: '',
})

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        form.reset()
        form.clearErrors()
    }
})

function submit() {
    if (!props.documento) return;
    form.post(`/bandeja-entrada/${props.documento.id}/derivar`, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false
            emit('submitted')
        },
    })
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Derivar Documento</DialogTitle>
                <DialogDescription>
                    Seleccione el área de destino y añada un proveído.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit">
                <div class="grid gap-4 py-4">
                    <div>
                        <Label for="area_destino_id">Área de Destino</Label>
                        <Select v-model="form.area_destino_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Seleccione un área" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="area in areasOptions" :key="area.id" :value="area.id">
                                    {{ area.nombre }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.area_destino_id" class="mt-2" />
                    </div>

                    <div>
                        <Label for="proveido">Proveído / Comentario</Label>
                        <Textarea id="proveido" v-model="form.proveido" placeholder="Añadir un comentario..." />
                        <InputError :message="form.errors.proveido" class="mt-2" />
                    </div>
                </div>
            </form>

            <DialogFooter>
                <Button variant="outline" @click="open = false" :disabled="form.processing">
                    Cancelar
                </Button>
                <Button @click="submit" :disabled="form.processing">
                    <span v-if="form.processing">Derivando...</span>
                    <span v-else>Derivar</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
