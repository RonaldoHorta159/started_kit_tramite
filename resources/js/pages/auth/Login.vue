<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    // canResetPassword ya no es necesario porque eliminamos la funcionalidad
}>();
</script>

<template>
    <AuthBase title="Inicia sesión en tu cuenta" description="Ingresa tu DNI y contraseña para continuar">

        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <Form v-bind="AuthenticatedSessionController.store.form()" :reset-on-success="['password']"
            v-slot="{ errors, processing }" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="dni">DNI</Label>
                    <Input id="dni" type="text" name="dni" required autofocus autocomplete="username"
                        placeholder="Número de DNI" />
                    <InputError :message="errors.dni" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Contraseña</Label>
                    </div>
                    <Input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Contraseña" />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" />
                        <span>Recordarme</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :disabled="processing" data-test="login-button">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Ingresar
                </Button>
            </div>
        </Form>
    </AuthBase>
</template>
