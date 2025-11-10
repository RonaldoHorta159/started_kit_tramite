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
import { X, Loader2 } from 'lucide-vue-next'

// --- Props y Emits ---

const props = defineProps({
    open: Boolean,
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

// --- Estado del Correlativo ---
const nroDocumentoMostrado = ref('')
const isLoadingCorrelative = ref(false)

// --- Variables para Debounce y Abort ---
let aborter = null
let timer = null

// --- Formulario ---

const fileInput = ref(null)

const form = useForm({
    tipo_documento_id: null,
    area_destino_id: null,
    asunto: '',
    folios: 1,
    prioridad: 'Normal',
    archivo: null,
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
    // 2. CORRECCIÓN: Usamos la URL estática
    form.post('/emitir', {
        onSuccess: () => {
            emit('created')
        },
    })
}

// 4. Lógica Asíncrona MEJORADA con AbortController
async function fetchCorrelativeNumber(tipoId) {
    if (!tipoId) {
        nroDocumentoMostrado.value = ''
        return
    }

    if (aborter) {
        aborter.abort()
    }
    aborter = new AbortController()

    isLoadingCorrelative.value = true
    nroDocumentoMostrado.value = 'Cargando...'

    try {
        // 3. CORRECCIÓN: Usamos la URL estática con template literal
        const response = await fetch(`/correlatives/${tipoId}`, {
            signal: aborter.signal
        })

        if (!response.ok) {
            // No lanzamos un error para evitar un unhandled rejection en consola.
            // Manejamos explícitamente según el status HTTP.
            console.warn(`fetchCorrelativeNumber: respuesta ${response.status}`)
            if (response.status === 404) {
                // No existe correlativo todavía
                nroDocumentoMostrado.value = '—'
            } else {
                nroDocumentoMostrado.value = 'Error'
            }
            return
        }

        const data = await response.json()
        nroDocumentoMostrado.value = data.numero_formateado ?? '—'

    } catch (error) {
        if (error.name !== 'AbortError') {
            console.error('Error al obtener correlativo:', error)
            nroDocumentoMostrado.value = 'Error'
        }
    } finally {
        if (!aborter.signal.aborted) {
            isLoadingCorrelative.value = false
        }
    }
}

// 5. "Watcher" MEJORADO con Debounce (setTimeout)
watch(() => form.tipo_documento_id, (newTipoId) => {
    if (timer) {
        clearTimeout(timer)
    }
    timer = setTimeout(() => {
        fetchCorrelativeNumber(newTipoId)
    }, 300)
})

// Resetea todo cuando se abre el modal
watch(() => props.open, (newVal) => {
    if (newVal) {
        form.reset()
        form.clearErrors()
        clearFile()
        nroDocumentoMostrado.value = ''
        isLoadingCorrelative.value = false
        if (timer) clearTimeout(timer)
        if (aborter) aborter.abort()
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
                            <Select v-model="form.tipo_documento_id" :disabled="isLoadingCorrelative">
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
                            <Label for="nro_documento">N° Documento</Label>
                            <div class="relative">
                                <Input id="nro_documento" type="text" :value="nroDocumentoMostrado" disabled
                                    placeholder="Seleccione un tipo..." />
                                <Loader2 v-if="isLoadingCorrelative"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 animate-spin" />
                            </div>
                        </div>
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
                <Button type="submit" form="create-document-form" :disabled="form.processing || isLoadingCorrelative">
                    <span v-if="form.processing">Guardando...</span>
                    <span v-else>Guardar</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
