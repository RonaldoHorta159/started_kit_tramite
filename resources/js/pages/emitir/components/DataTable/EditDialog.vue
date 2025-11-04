<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, watchEffect } from 'vue'
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

const props = defineProps({
    open: Boolean,
    documento: { // El documento que vamos a editar
        type: Object,
        default: null,
    },
    areas: {
        type: Array,
        default: () => [],
    },
    tiposDocumento: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['update:open', 'updated'])

// --- Formulario ---

const fileInput = ref(null)

// Campos del formulario (deben coincidir con UpdateDocumentoRequest)
const form = useForm({
    _method: 'PUT', // Clave para que Inertia y Laravel entiendan que es un PUT
    tipo_documento_id: null,
    area_destino_id: null, // En tu lógica, este es el area_actual_id
    asunto: '',
    folios: 1,
    prioridad: 'Normal',
    archivo: null, // Solo se enviará si el usuario sube un *nuevo* archivo
    parent_id: null,
})

// --- Funciones ---

function onFileChange(event) {
    form.archivo = event.target.files[0]
}

function clearFile() {
    form.archivo = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

function submit() {
    if (!props.documento) return

    form.post(route('emitir.update', props.documento.id), {
        onSuccess: () => {
            emit('updated')
        },
        onError: () => {
            // Los errores de validación se manejan automáticamente
        },
    })
}

// Observa cuando el modal se abre y si hay un documento
watchEffect(() => {
    if (props.open && props.documento) {
        // Rellenamos el formulario con los datos del documento
        form.reset() // Limpia estado anterior
        form.tipo_documento_id = props.documento.tipo_documento_id
        form.area_destino_id = props.documento.area_actual_id // El destino es el 'area_actual'
        form.asunto = props.documento.asunto
        form.folios = props.documento.folios
        form.prioridad = props.documento.prioridad
        form.parent_id = props.documento.parent_id

        // Importante: reseteamos el 'archivo' a null
        // El usuario debe subir uno nuevo si quiere cambiarlo.
        form.archivo = null
        form.clearErrors()
        clearFile()
    }
})

function closeModal() {
    emit('update:open', false)
}
</script>

<template>
    <Dialog :open="open" @update:open="closeModal">
        <DialogContent class="sm:max-w-[625px]">
            <DialogHeader>
                <DialogTitle>Editar Documento</DialogTitle>
                <DialogDescription>
                    Modifique los campos necesarios y guarde los cambios.
                </DialogDescription>
            </DialogHeader>

            <form id="edit-document-form" @submit.prevent="submit">
                <div class="grid gap-4 py-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label for="edit_tipo_documento_id">Tipo de Documento</Label>
                            <Select v-model="form.tipo_documento_id">
                                <SelectTrigger id="edit_tipo_documento_id">
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
                            <Label for="edit_area_destino_id">Área de Destino</Label>
                            <Select v-model="form.area_destino_id">
                                <SelectTrigger id="edit_area_destino_id">
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
                        <Label for="edit_asunto">Asunto</Label>
                        <Textarea id="edit_asunto" v-model="form.asunto" />
                        <p v-if="form.errors.asunto" class="text-sm text-red-600 mt-1">
                            {{ form.errors.asunto }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label for="edit_folios">N° de Folios</Label>
                            <Input id="edit_folios" v-model="form.folios" type="number" min="1" />
                            <p v-if="form.errors.folios" class="text-sm text-red-600 mt-1">
                                {{ form.errors.folios }}
                            </p>
                        </div>
                        <div>
                            <Label for="edit_prioridad">Prioridad</Label>
                            <Select v-model="form.prioridad">
                                <SelectTrigger id="edit_prioridad">
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
                        <Label for="edit_archivo">Cambiar Archivo (Opcional)</Label>
                        <Input id="edit_archivo" ref="fileInput" type="file" @change="onFileChange" accept=".pdf"
                            class="file:text-sm file:font-medium" />

                        <div v-if="documento?.archivo_path && !form.archivo" class="mt-2 text-sm text-muted-foreground">
                            Archivo actual: {{ documento.archivo_path.split('/').pop() }}
                        </div>

                        <div v-if="form.archivo" class="mt-2 text-sm flex items-center justify-between">
                            <span class="truncate">Nuevo: {{ form.archivo.name }}</span>
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
                <Button type="submit" form="edit-document-form" :disabled="form.processing">
                    <span v-if="form.processing">Actualizando...</span>
                    <span v-else>Actualizar</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
