<style>
    .fi-sidebar-header {
        height: auto !important;
        padding: 1rem 1.5rem !important;
    }

    .fi-logo {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        height: auto !important;
    }

    .fi-logo img {
        height: 2.5rem !important;
        width: auto !important;
        max-width: none !important;
    }

    .fi-logo-text {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
    }

    .fi-logo-title {
        font-size: 1.125rem !important;
        font-weight: 600 !important;
        color: #1f2937 !important;
        line-height: 1.2 !important;
        margin: 0 !important;
    }

    .fi-logo-subtitle {
        font-size: 0.75rem !important;
        color: #6b7280 !important;
        line-height: 1 !important;
        margin: 0 !important;
        margin-top: 0.125rem !important;
    }

    .dark .fi-logo-title {
        color: #f9fafb !important;
    }

    .dark .fi-logo-subtitle {
        color: #9ca3af !important;
    }

    @media (max-width: 1024px) {
        .fi-logo-text {
            display: none !important;
        }

        .fi-logo img {
            height: 2rem !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscar el logo existente y modificarlo
    const logoLink = document.querySelector('.fi-sidebar-header a');
    if (logoLink) {
        const img = logoLink.querySelector('img');
        if (img) {
            // Crear el contenedor de texto
            const textContainer = document.createElement('div');
            textContainer.className = 'fi-logo-text';

            const title = document.createElement('div');
            title.className = 'fi-logo-title';
            title.textContent = '{{ app_name() }}';

            const subtitle = document.createElement('div');
            subtitle.className = 'fi-logo-subtitle';
            subtitle.textContent = 'v{{ app_version() }}';

            textContainer.appendChild(title);
            textContainer.appendChild(subtitle);

            // Agregar el texto después de la imagen
            logoLink.appendChild(textContainer);

            // Asegurar que el contenedor tenga la clase correcta
            logoLink.classList.add('fi-logo');
        }
    }
});
</script>
