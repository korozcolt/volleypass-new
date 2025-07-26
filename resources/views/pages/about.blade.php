<x-layouts.public>
    <x-slot name="title">Acerca de VolleyPass Sucre</x-slot>

    <div class="py-16">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Acerca de VolleyPass Sucre
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        La plataforma digital oficial para el voleibol en Sucre
                    </p>
                </div>

                <div class="prose prose-lg max-w-none dark:prose-invert">
                    <h2>Nuestra Misión</h2>
                    <p>
                        VolleyPass Sucre es la plataforma digital oficial de la Liga de Voleibol de Sucre,
                        diseñada para modernizar y transparentar la gestión deportiva en nuestra región.
                    </p>

                    <h2>¿Qué Ofrecemos?</h2>
                    <ul>
                        <li><strong>Torneos en Vivo:</strong> Seguimiento en tiempo real de todos los partidos</li>
                        <li><strong>Carnet Digital:</strong> Identificación oficial para jugadoras</li>
                        <li><strong>Estadísticas:</strong> Análisis detallado del rendimiento deportivo</li>
                        <li><strong>Transparencia:</strong> Información pública de resultados y clasificaciones</li>
                    </ul>

                    <h2>Para Jugadoras</h2>
                    <p>
                        Las jugadoras registradas pueden acceder a su dashboard personal donde encontrarán:
                    </p>
                    <ul>
                        <li>Su carnet digital oficial</li>
                        <li>Estadísticas personales detalladas</li>
                        <li>Calendario de partidos y entrenamientos</li>
                        <li>Información de sus equipos y torneos</li>
                    </ul>

                    <h2>Contacto</h2>
                    <p>
                        Para más información, puedes contactarnos en:
                    </p>
                    <ul>
                        <li>Email: info@volleypasssucre.com</li>
                        <li>Teléfono: +57 300 123 4567</li>
                        <li>Dirección: Sucre, Colombia</li>
                    </ul>
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver a Torneos
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
