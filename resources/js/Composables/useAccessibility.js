import { ref, onMounted } from 'vue';

export function useAccessibility() {
    const fontSize = ref(localStorage.getItem('fontSize') || 'normal');
    const contrast = ref(localStorage.getItem('contrast') || 'normal');

    // Aplicar tamaño de fuente
    const applyFontSize = (size) => {
        document.documentElement.setAttribute('data-font-size', size);
        fontSize.value = size;
        localStorage.setItem('fontSize', size);
    };

    // Aplicar contraste
    const applyContrast = (contrastLevel) => {
        document.documentElement.setAttribute('data-contrast', contrastLevel);
        contrast.value = contrastLevel;
        localStorage.setItem('contrast', contrastLevel);
    };

    // Cambiar tamaño de fuente
    const setFontSize = (size) => {
        applyFontSize(size);
    };

    // Cambiar contraste
    const setContrast = (contrastLevel) => {
        applyContrast(contrastLevel);
    };

    // Resetear configuración
    const resetAccessibility = () => {
        applyFontSize('normal');
        applyContrast('normal');
    };

    // Inicializar
    onMounted(() => {
        applyFontSize(fontSize.value);
        applyContrast(contrast.value);
    });

    return {
        fontSize,
        contrast,
        setFontSize,
        setContrast,
        resetAccessibility,
        fontSizes: [
            { value: 'small', label: 'Pequeño' },
            { value: 'normal', label: 'Normal' },
            { value: 'large', label: 'Grande' }
        ],
        contrastLevels: [
            { value: 'normal', label: 'Normal' },
            { value: 'high', label: 'Alto Contraste' }
        ]
    };
}
