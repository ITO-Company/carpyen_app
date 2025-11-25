<script setup>
import { useTheme } from '@/Composables/useTheme';
import { useAccessibility } from '@/Composables/useAccessibility';
import { ref } from 'vue';

const { currentTheme, currentMode, setTheme, setMode, enableAutoMode, disableAutoMode, themes } = useTheme();
const { fontSize, contrast, setFontSize, setContrast, fontSizes, contrastLevels } = useAccessibility();

const showSettings = ref(false);
const autoModeEnabled = ref(localStorage.getItem('autoMode') === 'true');

const toggleAutoMode = () => {
    if (autoModeEnabled.value) {
        enableAutoMode();
    } else {
        disableAutoMode();
        setMode('light');
    }
};

const themeLabels = {
    azul: 'Azul',
    verde: 'Verde',
    violeta: 'Violeta'
};
</script>

<template>
    <div class="theme-settings">
        <!-- Botón para abrir configuración -->
        <button 
            @click="showSettings = !showSettings" 
            class="settings-button"
            title="Configuración de Tema y Accesibilidad"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6m5.2-13.2l-4.2 4.2m0 6l4.2 4.2M23 12h-6m-6 0H1m18.2 5.2l-4.2-4.2m-6 0l-4.2 4.2"></path>
            </svg>
        </button>

        <!-- Panel de configuración -->
        <transition name="slide-fade">
            <div v-if="showSettings" class="settings-panel">
                <div class="settings-header">
                    <h3>Configuración</h3>
                    <button @click="showSettings = false" class="close-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div class="settings-content">
                    <!-- Selección de Tema -->
                    <div class="setting-group">
                        <label class="setting-label">Tema de Color</label>
                        <div class="theme-options">
                            <button
                                v-for="theme in themes"
                                :key="theme"
                                @click="setTheme(theme)"
                                :class="['theme-option', `theme-${theme}`, { active: currentTheme === theme }]"
                                :title="themeLabels[theme]"
                            >
                                <span class="theme-color"></span>
                                <span class="theme-name">{{ themeLabels[theme] }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Modo Día/Noche -->
                    <div class="setting-group">
                        <label class="setting-label">Modo</label>
                        <div class="mode-toggle">
                            <button
                                @click="setMode('light')"
                                :class="['mode-button', { active: currentMode === 'light' && !autoModeEnabled }]"
                                :disabled="autoModeEnabled"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                                Día
                            </button>
                            <button
                                @click="setMode('dark')"
                                :class="['mode-button', { active: currentMode === 'dark' && !autoModeEnabled }]"
                                :disabled="autoModeEnabled"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                                Noche
                            </button>
                        </div>
                        <label class="checkbox-label">
                            <input 
                                type="checkbox" 
                                v-model="autoModeEnabled"
                                @change="toggleAutoMode"
                            />
                            <span>Modo automático (según hora)</span>
                        </label>
                    </div>

                    <!-- Tamaño de Fuente -->
                    <div class="setting-group">
                        <label class="setting-label">Tamaño de Texto</label>
                        <div class="font-size-options">
                            <button
                                v-for="size in fontSizes"
                                :key="size.value"
                                @click="setFontSize(size.value)"
                                :class="['font-size-button', { active: fontSize === size.value }]"
                            >
                                {{ size.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Contraste -->
                    <div class="setting-group">
                        <label class="setting-label">Contraste</label>
                        <div class="contrast-options">
                            <button
                                v-for="level in contrastLevels"
                                :key="level.value"
                                @click="setContrast(level.value)"
                                :class="['contrast-button', { active: contrast === level.value }]"
                            >
                                {{ level.label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Overlay -->
        <transition name="fade">
            <div v-if="showSettings" @click="showSettings = false" class="settings-overlay"></div>
        </transition>
    </div>
</template>

<style scoped>
.theme-settings {
    position: relative;
}

.settings-button {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background-color: var(--theme-primary);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-lg);
    transition: all var(--transition-fast);
    z-index: 1000;
}

.settings-button:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: var(--shadow-xl);
}

.settings-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 400px;
    height: 100vh;
    background-color: var(--theme-bg-primary);
    box-shadow: var(--shadow-xl);
    z-index: 1001;
    overflow-y: auto;
}

.settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-6);
    border-bottom: 1px solid var(--theme-border);
}

.settings-header h3 {
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--theme-text-primary);
}

.close-button {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--theme-text-secondary);
    padding: var(--spacing-2);
    border-radius: var(--border-radius-md);
    transition: all var(--transition-fast);
}

.close-button:hover {
    background-color: var(--theme-bg-secondary);
    color: var(--theme-text-primary);
}

.settings-content {
    padding: var(--spacing-6);
}

.setting-group {
    margin-bottom: var(--spacing-6);
}

.setting-label {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-3);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.theme-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-3);
}

.theme-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-2);
    padding: var(--spacing-3);
    border: 2px solid var(--theme-border);
    border-radius: var(--border-radius-lg);
    background: none;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.theme-option:hover {
    border-color: var(--theme-primary);
    transform: translateY(-2px);
}

.theme-option.active {
    border-color: var(--theme-primary);
    background-color: var(--theme-bg-secondary);
}

.theme-color {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
}

.theme-azul .theme-color {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
}

.theme-verde .theme-color {
    background: linear-gradient(135deg, #10b981, #34d399);
}

.theme-violeta .theme-color {
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
}

.theme-name {
    font-size: var(--font-size-xs);
    color: var(--theme-text-primary);
}

.mode-toggle {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-2);
    margin-bottom: var(--spacing-3);
}

.mode-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-2);
    padding: var(--spacing-3);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-md);
    background-color: var(--theme-bg-primary);
    color: var(--theme-text-primary);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.mode-button:hover:not(:disabled) {
    border-color: var(--theme-primary);
    background-color: var(--theme-bg-secondary);
}

.mode-button.active {
    border-color: var(--theme-primary);
    background-color: var(--theme-primary);
    color: white;
}

.mode-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
}

.font-size-options,
.contrast-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: var(--spacing-2);
}

.font-size-button,
.contrast-button {
    padding: var(--spacing-3);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-md);
    background-color: var(--theme-bg-primary);
    color: var(--theme-text-primary);
    cursor: pointer;
    transition: all var(--transition-fast);
    font-size: var(--font-size-sm);
}

.font-size-button:hover,
.contrast-button:hover {
    border-color: var(--theme-primary);
    background-color: var(--theme-bg-secondary);
}

.font-size-button.active,
.contrast-button.active {
    border-color: var(--theme-primary);
    background-color: var(--theme-primary);
    color: white;
}

.settings-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

/* Animaciones */
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
