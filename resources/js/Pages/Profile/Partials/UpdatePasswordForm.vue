<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_contrasena: '',
    contrasena: '',
    contrasena_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.contrasena) {
                form.reset('contrasena', 'contrasena_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_contrasena) {
                form.reset('current_contrasena');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Update Password
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_contrasena" value="Contraseña Actual" />

                <TextInput
                    id="current_contrasena"
                    ref="currentPasswordInput"
                    v-model="form.current_contrasena"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_contrasena"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="contrasena" value="Nueva Contraseña" />

                <TextInput
                    id="contrasena"
                    ref="passwordInput"
                    v-model="form.contrasena"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.contrasena" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="contrasena_confirmation"
                    value="Confirmar Contraseña"
                />

                <TextInput
                    id="contrasena_confirmation"
                    v-model="form.contrasena_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.contrasena_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
