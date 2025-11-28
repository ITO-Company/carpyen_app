<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('cliente.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Portal de Clientes - Iniciar Sesión" />

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="48"
                        height="48"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h1>Portal de Clientes</h1>
                <p>Accede a tus proyectos y planes de pago</p>
            </div>

            <form @submit.prevent="submit" class="login-form">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="tu@email.com"
                            required
                            autofocus
                        />
                    </div>
                    <span v-if="form.errors.email" class="error-message">{{ form.errors.email }}</span>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="••••••••"
                            required
                        />
                    </div>
                    <span v-if="form.errors.password" class="error-message">{{ form.errors.password }}</span>
                </div>

                <div class="form-group-checkbox">
                    <label>
                        <input v-model="form.remember" type="checkbox" />
                        <span>Recordarme</span>
                    </label>
                </div>

                <button type="submit" class="submit-btn" :disabled="form.processing">
                    <span v-if="!form.processing">Iniciar Sesión</span>
                    <span v-else>Iniciando...</span>
                </button>
            </form>

            <div class="login-footer">
                <p>¿Necesitas ayuda? Contacta a tu vendedor</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--theme-bg-secondary);
    padding: 2rem;
}

.login-card {
    width: 100%;
    max-width: 450px;
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-xl);
    padding: 3rem;
    box-shadow: var(--shadow-xl);
}

.login-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.logo-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--theme-primary), var(--theme-primary-light));
    border-radius: var(--border-radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.login-header h1 {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--theme-text-primary);
    margin-bottom: 0.5rem;
}

.login-header p {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--theme-text-primary);
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper svg {
    position: absolute;
    left: 1rem;
    color: var(--theme-text-tertiary);
    pointer-events: none;
}

.input-wrapper input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-md);
    color: var(--theme-text-primary);
    font-size: var(--font-size-base);
    transition: all var(--transition-fast);
}

.input-wrapper input:focus {
    outline: none;
    border-color: var(--theme-primary);
    box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.1);
}

.input-wrapper input::placeholder {
    color: var(--theme-text-tertiary);
}

.error-message {
    font-size: var(--font-size-sm);
    color: var(--theme-danger);
}

.form-group-checkbox {
    display: flex;
    align-items: center;
}

.form-group-checkbox label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
}

.form-group-checkbox input[type="checkbox"] {
    width: 1.125rem;
    height: 1.125rem;
    cursor: pointer;
    accent-color: var(--theme-primary);
}

.submit-btn {
    width: 100%;
    padding: 1rem;
    background: var(--theme-primary);
    color: white;
    border: none;
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-base);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.submit-btn:hover:not(:disabled) {
    background: var(--theme-primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.login-footer {
    margin-top: 2rem;
    text-align: center;
}

.login-footer p {
    font-size: var(--font-size-sm);
    color: var(--theme-text-tertiary);
}

/* Responsive */
@media (max-width: 640px) {
    .login-card {
        padding: 2rem;
    }

    .logo-icon {
        width: 64px;
        height: 64px;
    }

    .login-header h1 {
        font-size: var(--font-size-xl);
    }
}
</style>
