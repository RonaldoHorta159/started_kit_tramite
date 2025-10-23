<script setup>
import { computed, onMounted, onUpdated, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue, SelectGroup } from '@/components/ui/select'

const props = defineProps({
    open: { type: Boolean, default: false }, // v-model:open
    item: { type: Object, default: null },   // tipo de documento a editar, o null para crear
    estadosOptions: { type: Array, default: () => [] }, // ['Activo','Inactivo'] o [{value,label}]
})
const emit = defineEmits(['update:open', 'saved'])

const open = computed({
    get: () => props.open,
    set: (v) => emit('update:open', v),
})

// helpers para options que pueden venir como string u objeto
const normVal = (x) => (typeof x === 'object' ? x.value : x)
const labelOf = (x) => (typeof x === 'object' ? x.label : x)

// Form
const form = useForm({
    nombre: '',
    estado: 'Activo',
})

// ---- Hidratación sin watch (igual patrón que Users) ----
const lastKey = ref(null)

function hydrateFromItem(it) {
    form.reset('nombre', 'estado')

    if (!it) {
        form.estado = 'Activo'
        return
    }

    form.nombre = it.nombre ?? ''
    const e = props.estadosOptions.find(e => normVal(e) === it.estado || labelOf(e) === it.estado)
    form.estado = e ? normVal(e) : (it.estado ?? 'Activo')
}

function initIfNeeded() {
    const key = `${props.open ? 'open' : 'closed'}:${props.item?.id ?? 'create'}`
    if (key !== lastKey.value) {
        lastKey.value = key
        if (props.open) hydrateFromItem(props.item)
    }
}

onMounted(initIfNeeded)
onUpdated(initIfNeeded)
// -------------------------------------------------------

function submitForm() {
    const baseUrl = '/tipos-documento' // ajusta si tu prefijo difiere
    const onSuccess = () => {
        toast.success(props.item ? 'Tipo de documento actualizado' : 'Tipo de documento creado')
        open.value = false
        emit('saved')
        router.get(`${baseUrl}${window.location.search}`, {}, { preserveScroll: true, preserveState: false })
    }
    const onError = () => toast.error('Ocurrió un error al guardar.')

    if (props.item?.id) {
        form.put(`${baseUrl}/${props.item.id}`, {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onSuccess,
            onError,
        })
    } else {
        form.post(baseUrl, {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onSuccess,
            onError,
        })
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <!-- re-montaje visual para limpiar estado interno al cambiar item -->
        <DialogContent :key="`${open ? 'open' : 'closed'}:${item?.id ?? 'create'}`"
            class="sm:max-w-[520px] grid-rows-[auto_1fr_auto]">
            <DialogHeader>
                <DialogTitle>{{ item ? 'Editar tipo de documento' : 'Crear tipo de documento' }}</DialogTitle>
                <DialogDescription>Completa los campos y guarda para finalizar.</DialogDescription>
            </DialogHeader>

            <div class="py-4 overflow-y-auto px-1">
                <form class="grid grid-cols-1 gap-4" @submit.prevent>
                    <div>
                        <Label for="nombre">Nombre</Label>
                        <Input id="nombre" v-model="form.nombre" class="mt-5" />
                        <span v-if="form.errors.nombre" class="text-red-500 text-sm">{{ form.errors.nombre }}</span>
                    </div>

                    <div>
                        <Label>Estado</Label>
                        <Select v-model="form.estado">
                            <SelectTrigger>
                                <SelectValue placeholder="Seleccionar estado" />
                            </SelectTrigger>
                            <SelectContent class="mt-5">
                                <SelectGroup>
                                    <SelectItem v-for="opt in estadosOptions" :key="normVal(opt)" :value="normVal(opt)">
                                        {{ labelOf(opt) }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.estado" class="text-red-500 text-sm">{{ form.errors.estado }}</span>
                    </div>
                </form>
            </div>

            <DialogFooter>
                <Button variant="outline" :disabled="form.processing" @click="open = false">Cancelar</Button>
                <Button :disabled="form.processing" @click="submitForm">
                    {{ form.processing ? 'Guardando…' : 'Guardar' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
