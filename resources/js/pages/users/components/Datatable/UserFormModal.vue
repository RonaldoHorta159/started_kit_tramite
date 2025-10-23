<script setup>
import { computed, onMounted, onUpdated, ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog'
import {
    Select, SelectContent, SelectItem, SelectTrigger, SelectValue, SelectGroup,
} from '@/components/ui/select'
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
    open: { type: Boolean, default: false },             // v-model:open
    user: { type: Object, default: null },
    areasOptions: { type: Array, default: () => [] },    // [{id,nombre}]
    rolesOptions: { type: Array, default: () => [] },    // ['Admin',..] o [{value,label}]
    estadosOptions: { type: Array, default: () => [] },  // ['Activo',..] o [{value,label}]
})

const emit = defineEmits(['update:open', 'saved'])

const open = computed({
    get: () => props.open,
    set: (v) => emit('update:open', v),
})

// helpers
const normVal = (x) => typeof x === 'object' ? x.value : x
const labelOf = (x) => typeof x === 'object' ? x.label : x

const form = useForm({
    dni: '',
    nombres: '',
    apellido_paterno: '',
    apellido_materno: '',
    email: '',
    celular: '',
    primary_area_id: null,
    // 🔹 NUEVO: áreas adicionales (pivot)
    areas_ids: [],
    rol: null,
    estado: 'Activo',
    password: '',
    password_confirmation: '',
})

// ---- Hidratación sin watch ----
const lastKey = ref(null)

function hydrateFromUser(u) {
    form.reset(
        'dni', 'nombres', 'apellido_paterno', 'apellido_materno',
        'email', 'celular', 'primary_area_id', 'rol', 'estado',
        'password', 'password_confirmation', 'areas_ids',
    )

    if (!u) { // crear
        form.estado = 'Activo'
        form.areas_ids = []
        return
    }

    // editar
    form.dni = u.dni ?? ''
    form.nombres = u.nombres ?? ''
    form.apellido_paterno = u.apellido_paterno ?? ''
    form.apellido_materno = u.apellido_materno ?? ''
    form.email = u.email ?? ''
    form.celular = u.celular ?? ''
    form.primary_area_id = u.primary_area_id ? String(u.primary_area_id) : null

    // 🔹 Cargar áreas adicionales desde la relación eager loaded (u.areas)
    form.areas_ids = Array.isArray(u?.areas) ? u.areas.map(a => String(a.id)) : []

    const r = props.rolesOptions.find(r => normVal(r) === u.rol || labelOf(r) === u.rol)
    form.rol = r ? normVal(r) : (u.rol ?? null)

    const e = props.estadosOptions.find(e => normVal(e) === u.estado || labelOf(e) === u.estado)
    form.estado = e ? normVal(e) : (u.estado ?? 'Activo')

    form.password = ''
    form.password_confirmation = ''
}

function initIfNeeded() {
    const key = `${props.open ? 'open' : 'closed'}:${props.user?.id ?? 'create'}`
    if (key !== lastKey.value) {
        lastKey.value = key
        if (props.open) hydrateFromUser(props.user)
    }
}

onMounted(initIfNeeded)
onUpdated(initIfNeeded)

// --------------------- Áreas adicionales (UI/acciones) ---------------------
const areasSet = computed(() => new Set((form.areas_ids ?? []).map(v => String(v))))

function toggleArea(id, checked) {
    const s = new Set(areasSet.value)
    const key = String(id)
    checked ? s.add(key) : s.delete(key)
    form.areas_ids = Array.from(s) // mantiene como strings; backend normaliza a ints
}

function clearAreas() {
    form.areas_ids = []
}
// ---------------------------------------------------------------------------

function submitForm() {
    const onSuccess = () => {
        toast.success(props.user ? 'Usuario actualizado' : 'Usuario creado')
        open.value = false
        emit('saved')
        router.get(`/users${window.location.search}`, {}, { preserveScroll: true, preserveState: false })
    }
    const onError = () => toast.error('Ocurrió un error al guardar.')

    if (props.user?.id) {
        form.put(`/users/${props.user.id}`, {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onSuccess, onError,
        })
    } else {
        form.post('/users', {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onSuccess, onError,
        })
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <!-- re-montaje visual -->
        <DialogContent :key="`${open ? 'open' : 'closed'}:${user?.id ?? 'create'}`"
            class="sm:max-w-[600px] grid-rows-[auto_1fr_auto]">
            <DialogHeader>
                <DialogTitle>{{ user ? 'Editar Usuario' : 'Crear Usuario' }}</DialogTitle>
                <DialogDescription>Completa los datos del usuario. Haz clic en guardar para finalizar.
                </DialogDescription>
            </DialogHeader>

            <div class="py-4 overflow-y-auto px-1">
                <form class="grid grid-cols-2 gap-4" @submit.prevent>
                    <div>
                        <Label for="dni">DNI</Label>
                        <Input id="dni" v-model="form.dni" />
                        <span v-if="form.errors.dni" class="text-red-500 text-sm">{{ form.errors.dni }}</span>
                    </div>

                    <div>
                        <Label for="nombres">Nombres</Label>
                        <Input id="nombres" v-model="form.nombres" />
                        <span v-if="form.errors.nombres" class="text-red-500 text-sm">{{ form.errors.nombres }}</span>
                    </div>

                    <div>
                        <Label for="apellido_paterno">Apellido Paterno</Label>
                        <Input id="apellido_paterno" v-model="form.apellido_paterno" />
                        <span v-if="form.errors.apellido_paterno" class="text-red-500 text-sm">
                            {{ form.errors.apellido_paterno }}
                        </span>
                    </div>

                    <div>
                        <Label for="apellido_materno">Apellido Materno</Label>
                        <Input id="apellido_materno" v-model="form.apellido_materno" />
                        <span v-if="form.errors.apellido_materno" class="text-red-500 text-sm">
                            {{ form.errors.apellido_materno }}
                        </span>
                    </div>

                    <div class="col-span-2">
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" v-model="form.email" />
                        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                    </div>

                    <div>
                        <Label for="celular">Celular</Label>
                        <Input id="celular" v-model="form.celular" />
                        <span v-if="form.errors.celular" class="text-red-500 text-sm">{{ form.errors.celular }}</span>
                    </div>

                    <div>
                        <Label>Área Principal</Label>
                        <Select v-model="form.primary_area_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Seleccionar área" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="a in areasOptions" :key="a.id" :value="String(a.id)">
                                        {{ a.nombre }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.primary_area_id" class="text-red-500 text-sm">
                            {{ form.errors.primary_area_id }}
                        </span>
                    </div>

                    <div class="col-span-2">
                        <Label>Áreas adicionales</Label>
                        <div class="flex items-center gap-2">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline" size="sm" class="h-9 border-dashed">
                                        Seleccionar áreas
                                        <Badge v-if="(form.areas_ids?.length || 0) > 0" variant="secondary"
                                            class="ml-2">
                                            {{ form.areas_ids.length }}
                                        </Badge>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="start" class="w-[260px] max-h-80 overflow-auto">
                                    <DropdownMenuCheckboxItem v-for="a in areasOptions" :key="a.id"
                                        :checked="areasSet.has(String(a.id))"
                                        @select.prevent="toggleArea(String(a.id), !areasSet.has(String(a.id)))">
                                        {{ a.nombre }}
                                    </DropdownMenuCheckboxItem>

                                    <DropdownMenuCheckboxItem v-if="form.areas_ids?.length" :checked="false"
                                        class="justify-center" @select.prevent="clearAreas">
                                        Limpiar selección
                                    </DropdownMenuCheckboxItem>
                                </DropdownMenuContent>
                            </DropdownMenu>

                            <!-- Chips de seleccionados -->
                            <div class="flex flex-wrap gap-2">
                                <Badge v-for="id in form.areas_ids" :key="id" variant="outline">
                                    {{(areasOptions.find(a => String(a.id) === String(id))?.nombre) ?? id}}
                                </Badge>
                            </div>
                        </div>

                        <!-- Errores de validación -->
                        <span v-if="form.errors['areas_ids'] || form.errors['areas_ids.0']"
                            class="text-red-500 text-sm">
                            {{ form.errors['areas_ids'] ?? 'Alguna de las áreas seleccionadas no es válida.' }}
                        </span>
                    </div>

                    <div>
                        <Label>Rol</Label>
                        <Select v-model="form.rol">
                            <SelectTrigger>
                                <SelectValue placeholder="Seleccionar rol" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="r in rolesOptions" :key="normVal(r)" :value="normVal(r)">
                                        {{ labelOf(r) }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.rol" class="text-red-500 text-sm">{{ form.errors.rol }}</span>
                    </div>

                    <div>
                        <Label>Estado</Label>
                        <Select v-model="form.estado">
                            <SelectTrigger>
                                <SelectValue placeholder="Seleccionar estado" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="e in estadosOptions" :key="normVal(e)" :value="normVal(e)">
                                        {{ labelOf(e) }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.estado" class="text-red-500 text-sm">{{ form.errors.estado }}</span>
                    </div>

                    <div class="col-span-2">
                        <Label for="password">Contraseña ({{ user ? 'Dejar en blanco para no cambiar' : 'Requerido'
                            }})</Label>
                        <Input id="password" type="password" v-model="form.password" />
                        <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
                    </div>

                    <div class="col-span-2">
                        <Label for="password_confirmation">Confirmar Contraseña</Label>
                        <Input id="password_confirmation" type="password" v-model="form.password_confirmation" />
                    </div>
                </form>
            </div>

            <DialogFooter>
                <Button variant="outline" :disabled="form.processing" @click="open = false">Cancelar</Button>
                <Button :disabled="form.processing" @click="submitForm">
                    {{ form.processing ? 'Guardando…' : 'Guardar Cambios' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
