<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { X } from 'lucide-vue-next'

// --- Props y Emits ---

// Este componente usa 'v-model:open' para ser controlado desde su padre
const props = defineProps({
    open: Boolean,
    // Necesitamos que nos pasen las listas de áreas y tipos
    // para rellenar los <Select>
    areas: {
        type: Array,
        default: () => [],
    },
    tiposDocumento: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['update:open', 'created'])

// --- Formulario ---

// Referencia para el input de archivo
const fileInput = ref(null)

// Definimos el formulario con 'useForm'
// Los campos deben coincidir con el 'StoreDocumentoRequest'
const form = useForm({
    tipo_documento_id: null,
    area_destino_id: null,
    asunto: '',
    folios: 1,
    prioridad: 'Normal',
    archivo: null,
    parent_id: null, // Opcional, para respuestas
})

// --- Funciones ---

// Maneja la subida del archivo
function onFileChange(event) {
    form.archivo = event.target.files[0]
}

// Limpia el archivo seleccionado
function clearFile() {
    form.archivo = null
    if (fileInput.value) {
        fileInput.value.value = '' // Resetea el input
    }
}

// Envía el formulario
function submit() {
    form.post(route('emitir.store'), {
        onSuccess: () => {
            emit('created') // Emite evento de éxito
            // El componente padre (DataTable/index.vue) se encargará de cerrar el modal
        },
        // onError se maneja automáticamente mostrando los errores
    })
}

// Observa la prop 'open'. Si se abre el modal, resetea el formulario.
watch(() => props.open, (newVal) => {
    if (newVal) {
        form.reset()
        form.clearErrors()
        clearFile()
    }
})

// Función para cerrar el modal
function closeModal() {
    emit('update:open', false)
}
</script>

<template>
    <Dialog :open="open" @update:open="closeModal">
        <DialogContent class="sm:max-w-[625px]">
            <DialogHeader>
                <DialogTitle>Emitir Nuevo Documento</DialogTitle>
                <DialogDescription>
                    Complete los campos para emitir un nuevo documento.
                </DialogDescription>
            </DialogHeader>

            <form id="create-document-form" @submit.prevent="submit">
                <div class="grid gap-4 py-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label for="tipo_documento_id">Tipo de Documento</Label>
                            <Select v-model="form.tipo_documento_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Seleccione un tipo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="tipo in tiposDocumento" :key="tipo.id" :value="tipo.id">
                                        {{ tipo.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.tipo_documento_id" class="text-sm text-red-600 mt-1">
                                {{ form.errors.tipo_documento_id }}
                            </p>
                        </div>
                        <div>
                            <Label for="area_destino_id">Área de Destino</Label>
                            <Select v-model="form.area_destino_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Seleccione un área" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="area in areas" :key="area.id" :value="area.id">
                                        {{ area.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.area_destino_id" class="text-sm text-red-600 mt-1">
                                {{ form.errors.area_destino_id }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <Label for="asunto">Asunto</Label>
                        <Textarea id="asunto" v-model="form.asunto" placeholder="Escriba el asunto del documento..." />
                        <p v-if="form.errors.asunto" class="text-sm text-red-600 mt-1">
                            {{ form.errors.asunto }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label for="folios">N° de Folios</Label>
                            <Input id="folios" v-model="form.folios" type="number" min="1" />
                            <p v-if="form.errors.folios" class="text-sm text-red-600 mt-1">
                                {{ form.errors.folios }}
                            </p>
                        </div>
                        <div>
                            <Label for="prioridad">Prioridad</Label>
                            <Select v-model="form.prioridad">
                                <SelectTrigger>
                                    <SelectValue placeholder="Seleccione prioridad" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Normal">Normal</SelectItem>
                                    <SelectItem value="Urgente">Urgente</SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.prioridad" class="text-sm text-red-600 mt-1">
                                {{ form.errors.prioridad }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <Label for="archivo">Archivo Adjunto (Opcional, PDF)</Label>
                        <Input id="archivo" ref="fileInput" type="file" @change="onFileChange" accept=".pdf"
                            class="file:text-sm file:font-medium" />

                        <div v-if="form.archivo" class="mt-2 text-sm flex items-center justify-between">
                            <span class="truncate">{{ form.archivo.name }}</span>
                            <Button type="button" variant="ghost" size="icon" @click="clearFile"
                                class="h-6 w-6 text-red-500 hover:text-red-500">
                                <X class="h-4 w-4" />
                            </Button>
                        </div>

                        <p v-if="form.errors.archivo" class="text-sm text-red-600 mt-1">
                            {{ form.errors.archivo }}
                        </p>
                    </div>

                </div>
            </form>

            <DialogFooter>
                <Button variant="outline" @click="closeModal" :disabled="form.processing">
                    Cancelar
                </Button>
                <Button type="submit" form="create-document-form" :disabled="form.processing">
                    <span v-if="form.processing">Guardando...</span>
                    <span v-else>Guardar</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
