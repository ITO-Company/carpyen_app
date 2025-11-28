<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const visitCount = ref(0);
const pageName = ref("");

const apiUrl = import.meta.env.VITE_API_URL || "http://localhost:8000";

onMounted(async () => {
    try {
        // Obtener el nombre de la página actual
        pageName.value = document.title || "Página";

        // Incrementar y obtener el contador
        const response = await axios.post(`/api/page-visit`, {
            page_name: pageName.value,
            page_url: window.location.pathname,
        });

        if (response.data.visit_count) {
            visitCount.value = response.data.visit_count;
        }
    } catch (error) {
        console.error("Error al registrar visita:", error);
    }
});
</script>

<template>
    <div class="page-counter">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        <span>{{ visitCount.toLocaleString() }} visitas</span>
    </div>
</template>

<style scoped>
.page-counter {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-xs);
    color: var(--theme-text-tertiary);
    padding: var(--spacing-2) var(--spacing-3);
    background-color: var(--theme-bg-secondary);
    border-radius: var(--border-radius-md);
}

.page-counter svg {
    flex-shrink: 0;
}
</style>
