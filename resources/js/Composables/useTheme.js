import { ref, onMounted, watch } from 'vue';

export function useTheme() {
    const currentTheme = ref(localStorage.getItem('theme') || 'azul');
    const currentMode = ref(localStorage.getItem('mode') || 'light');
    
    // Detectar modo día/noche basado en la hora
    const detectDayNightMode = () => {
        const hour = new Date().getHours();
        // Modo oscuro entre 20:00 y 06:00
        return (hour >= 20 || hour < 6) ? 'dark' : 'light';
    };

    // Aplicar tema
    const applyTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);
        currentTheme.value = theme;
        localStorage.setItem('theme', theme);
    };

    // Aplicar modo (light/dark)
    const applyMode = (mode) => {
        document.documentElement.setAttribute('data-mode', mode);
        currentMode.value = mode;
        localStorage.setItem('mode', mode);
    };

    // Cambiar tema
    const setTheme = (theme) => {
        applyTheme(theme);
    };

    // Cambiar modo
    const setMode = (mode) => {
        applyMode(mode);
    };

    // Activar modo automático día/noche
    const enableAutoMode = () => {
        const autoMode = detectDayNightMode();
        applyMode(autoMode);
        localStorage.setItem('autoMode', 'true');
        
        // Actualizar cada hora
        setInterval(() => {
            if (localStorage.getItem('autoMode') === 'true') {
                const newMode = detectDayNightMode();
                if (newMode !== currentMode.value) {
                    applyMode(newMode);
                }
            }
        }, 60000); // Cada minuto
    };

    // Desactivar modo automático
    const disableAutoMode = () => {
        localStorage.removeItem('autoMode');
    };

    // Inicializar
    onMounted(() => {
        applyTheme(currentTheme.value);
        
        // Si el modo automático está activado
        if (localStorage.getItem('autoMode') === 'true') {
            enableAutoMode();
        } else {
            applyMode(currentMode.value);
        }
    });

    return {
        currentTheme,
        currentMode,
        setTheme,
        setMode,
        enableAutoMode,
        disableAutoMode,
        themes: ['azul', 'verde', 'violeta']
    };
}
