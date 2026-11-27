<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';

defineOptions({
    layout: {
        title: 'Inicia sesion con tu cuenta',
        description: 'Ingresa tu usuario y contraseña para acceder al sistema',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Inicia sesion" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="nombre"
                    >Nombre</Label
                >
                <Input
                    id="nombre"
                    type="text"
                    name="nombre"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="username"
                    placeholder="Nombre o código"
                />
                <InputError :message="errors.nombre" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Contraseña</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                />
                <InputError :message="errors.password" />
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Iniciar sesion
            </Button>

            <Button
                type="button"
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="cancel-button"
            >
                <Spinner v-if="processing" />
                Cancelar
            </Button>
        </div>
    </Form>
</template>
