<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Carpyen - Diseño de Interiores" />

    <div class="landing-page">
        <!-- Header/Navigation -->
        <header class="header">
            <div class="container">
                <div class="nav-wrapper">
                    <div class="logo">
                        <svg
                            width="40"
                            height="40"
                            viewBox="0 0 40 40"
                            fill="none"
                        >
                            <rect
                                width="40"
                                height="40"
                                rx="8"
                                fill="url(#gradient)"
                            />
                            <path
                                d="M12 28V12L20 16L28 12V28L20 24L12 28Z"
                                fill="white"
                            />
                            <defs>
                                <linearGradient
                                    id="gradient"
                                    x1="0"
                                    y1="0"
                                    x2="40"
                                    y2="40"
                                >
                                    <stop offset="0%" stop-color="#8B5CF6" />
                                    <stop offset="100%" stop-color="#6366F1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <span class="logo-text">Carpyen</span>
                    </div>
                    <nav class="nav-links">
                        <a href="#nosotros">Nosotros</a>
                        <a href="#contacto">Contacto</a>
                    </nav>
                    <div class="auth-buttons" v-if="canLogin">
                        <Link :href="route('login')" class="btn-login"
                            >Iniciar Sesión</Link
                        >
                        <Link :href="route('cliente.login')" class="btn-client"
                            >Portal Cliente</Link
                        >
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1 class="hero-title">
                            Transformamos Espacios en
                            <span class="gradient-text"
                                >Experiencias Únicas</span
                            >
                        </h1>
                        <p class="hero-description">
                            Diseño de interiores personalizado que refleja tu
                            estilo y maximiza cada rincón de tu hogar u oficina.
                        </p>
                        <div class="hero-stats">
                            <div class="stat">
                                <div class="stat-number">500+</div>
                                <div class="stat-label">
                                    Proyectos Completados
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">98%</div>
                                <div class="stat-label">
                                    Clientes Satisfechos
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">15+</div>
                                <div class="stat-label">
                                    Años de Experiencia
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hero-image">
                        <div class="image-card card-1">
                            <img
                                src="https://www.hubinmobiliario.net/uploads/blog/1720409363.png"
                                alt="Diseño de Interiores Moderno"
                            />
                        </div>
                        <div class="image-card card-2">
                            <img
                                src="https://www.construyehogar.com/wp-content/uploads/2014/10/Decoraci%C3%B3n-de-interiores-de-departamento.jpg"
                                alt="Decoración de Interiores"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="servicios" class="services">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Nuestros Servicios</h2>
                    <p class="section-description">
                        Soluciones integrales para cada espacio de tu vida
                    </p>
                </div>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                                />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                        </div>
                        <h3 class="service-title">Diseño Residencial</h3>
                        <p class="service-description">
                            Creamos hogares que reflejan tu personalidad y
                            estilo de vida.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    x="2"
                                    y="7"
                                    width="20"
                                    height="14"
                                    rx="2"
                                    ry="2"
                                />
                                <path
                                    d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"
                                />
                            </svg>
                        </div>
                        <h3 class="service-title">Diseño Comercial</h3>
                        <p class="service-description">
                            Espacios funcionales que impulsan tu negocio y
                            marca.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"
                                />
                                <polyline
                                    points="3.27 6.96 12 12.01 20.73 6.96"
                                />
                                <line x1="12" y1="22.08" x2="12" y2="12" />
                            </svg>
                        </div>
                        <h3 class="service-title">Mobiliario a Medida</h3>
                        <p class="service-description">
                            Muebles únicos diseñados específicamente para tus
                            espacios.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                        </div>
                        <h3 class="service-title">Remodelación</h3>
                        <p class="service-description">
                            Renovamos y modernizamos tus espacios existentes.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"
                                />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                        </div>
                        <h3 class="service-title">Consultoría</h3>
                        <p class="service-description">
                            Asesoría experta para tus proyectos de diseño.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <svg
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="18"
                                    rx="2"
                                    ry="2"
                                />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                        </div>
                        <h3 class="service-title">Renders 3D</h3>
                        <p class="service-description">
                            Visualiza tu proyecto antes de construirlo.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <div class="cta-content">
                    <h2 class="cta-title">
                        ¿Listo para transformar tu espacio?
                    </h2>
                    <p class="cta-description">
                        Agenda una consulta gratuita y descubre cómo podemos
                        ayudarte
                    </p>
                    <a href="#contacto" class="btn-cta">
                        Comenzar Ahora
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-brand">
                        <div class="logo">
                            <svg
                                width="40"
                                height="40"
                                viewBox="0 0 40 40"
                                fill="none"
                            >
                                <rect
                                    width="40"
                                    height="40"
                                    rx="8"
                                    fill="url(#gradient2)"
                                />
                                <path
                                    d="M12 28V12L20 16L28 12V28L20 24L12 28Z"
                                    fill="white"
                                />
                                <defs>
                                    <linearGradient
                                        id="gradient2"
                                        x1="0"
                                        y1="0"
                                        x2="40"
                                        y2="40"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#8B5CF6"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#6366F1"
                                        />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <span class="logo-text">Carpyen</span>
                        </div>
                        <p class="footer-description">
                            Transformando espacios, creando experiencias.
                        </p>
                    </div>
                    <div class="footer-links">
                        <div class="footer-column">
                            <h4>Servicios</h4>
                            <a href="#servicios">Diseño Residencial</a>
                            <a href="#servicios">Diseño Comercial</a>
                            <a href="#servicios">Mobiliario</a>
                        </div>
                        <div class="footer-column">
                            <h4>Empresa</h4>
                            <a href="#nosotros">Sobre Nosotros</a>
                            <a href="#proyectos">Proyectos</a>
                            <a href="#contacto">Contacto</a>
                        </div>
                        <div class="footer-column">
                            <h4>Contacto</h4>
                            <a href="tel:+59173726149">+591 73726149</a>
                            <a href="mailto:info@carpyen.com"
                                >info@carpyen.com</a
                            >
                            <a href="#">Santa Cruz, Bolivia</a>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2025 Carpyen. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.landing-page {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI",
        sans-serif;
    background: #0f172a;
    color: #e2e8f0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Header */
.header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #1e293b;
    z-index: 100;
    padding: 1rem 0;
}

.nav-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-links {
    display: flex;
    gap: 2rem;
}

.nav-links a {
    color: #cbd5e1;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-links a:hover {
    color: #a78bfa;
}

.auth-buttons {
    display: flex;
    gap: 1rem;
}

.btn-login {
    padding: 0.625rem 1.25rem;
    color: #818cf8;
    text-decoration: none;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.3s;
}

.btn-login:hover {
    background: #1e293b;
}

.btn-client {
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    color: white;
    text-decoration: none;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.3s;
}

.btn-client:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(167, 139, 250, 0.4);
}

/* Hero Section */
.hero {
    padding: 8rem 0 6rem;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    color: #f1f5f9;
}

.gradient-text {
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-description {
    font-size: 1.25rem;
    color: #94a3b8;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 3rem;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    color: white;
    text-decoration: none;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px rgba(167, 139, 250, 0.5);
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: #1e293b;
    color: #a78bfa;
    text-decoration: none;
    font-weight: 600;
    border-radius: 0.75rem;
    border: 2px solid #334155;
    transition: all 0.3s;
}

.btn-secondary:hover {
    border-color: #a78bfa;
    background: #334155;
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.875rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.hero-image {
    position: relative;
    height: 500px;
}

.image-card {
    position: absolute;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.image-card:hover {
    transform: scale(1.05);
}

.card-1 {
    width: 350px;
    height: 280px;
    top: 0;
    left: 0;
    z-index: 2;
}

.card-2 {
    width: 300px;
    height: 240px;
    bottom: 0;
    right: 0;
    z-index: 1;
}

.image-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Services Section */
.services {
    padding: 6rem 0;
    background: #1e293b;
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    color: #f1f5f9;
}

.section-description {
    font-size: 1.125rem;
    color: #94a3b8;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.service-card {
    padding: 2rem;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 1rem;
    transition: all 0.3s;
}

.service-card:hover {
    border-color: #a78bfa;
    box-shadow: 0 10px 30px rgba(167, 139, 250, 0.3);
    transform: translateY(-5px);
}

.service-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: white;
}

.service-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: #f1f5f9;
}

.service-description {
    color: #94a3b8;
    line-height: 1.6;
}

/* CTA Section */
.cta {
    padding: 6rem 0;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
}

.cta-content {
    text-align: center;
    color: white;
}

.cta-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.cta-description {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1.25rem 2.5rem;
    background: #0f172a;
    color: #a78bfa;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.125rem;
    border-radius: 0.75rem;
    transition: all 0.3s;
}

.btn-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

/* Footer */
.footer {
    background: #020617;
    color: #e2e8f0;
    padding: 4rem 0 2rem;
}

.footer-content {
    display: grid;
    grid-template-columns: 2fr 3fr;
    gap: 4rem;
    margin-bottom: 3rem;
}

.footer-description {
    color: #64748b;
    margin-top: 1rem;
}

.footer-links {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.footer-column h4 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.footer-column a {
    display: block;
    color: #64748b;
    text-decoration: none;
    margin-bottom: 0.75rem;
    transition: color 0.3s;
}

.footer-column a:hover {
    color: #a78bfa;
}

.footer-bottom {
    padding-top: 2rem;
    border-top: 1px solid #1e293b;
    text-align: center;
    color: #64748b;
}

/* Responsive */
@media (max-width: 768px) {
    .nav-links {
        display: none;
    }

    .hero-content {
        grid-template-columns: 1fr;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .hero-image {
        display: none;
    }

    .services-grid {
        grid-template-columns: 1fr;
    }

    .footer-content {
        grid-template-columns: 1fr;
    }

    .footer-links {
        grid-template-columns: 1fr;
    }
}
</style>
