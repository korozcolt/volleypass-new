<!-- Estadísticas Principales -->
<div x-data="{
    jugadoras: 0,
    clubes: 0,
    ligas: 0,
    torneos: 0,
    partidos: 0,
    completitud: 0,
    init() {
        this.animateCounters();
    },
    animateCounters() {
        const targets = {
            jugadoras: {{ $jugadoras }},
            clubes: {{ $clubes }},
            ligas: {{ $ligas }},
            torneos: {{ $torneos }},
            partidos: {{ $partidos }},
            completitud: {{ $completitud }}
        };
        const duration = 2000;
        const steps = 60;
        const stepDuration = duration / steps;

        let currentStep = 0;
        const timer = setInterval(() => {
            currentStep++;
            const progress = currentStep / steps;
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);

            this.jugadoras = Math.floor(targets.jugadoras * easeOutQuart);
            this.clubes = Math.floor(targets.clubes * easeOutQuart);
            this.ligas = Math.floor(targets.ligas * easeOutQuart);
            this.torneos = Math.floor(targets.torneos * easeOutQuart);
            this.partidos = Math.floor(targets.partidos * easeOutQuart);
            this.completitud = Math.floor(targets.completitud * easeOutQuart);

            if (currentStep >= steps) {
                clearInterval(timer);
                this.jugadoras = targets.jugadoras;
                this.clubes = targets.clubes;
                this.ligas = targets.ligas;
                this.torneos = targets.torneos;
                this.partidos = targets.partidos;
                this.completitud = targets.completitud;
            }
        }, stepDuration);
    }
}" class="space-y-8">
    <!-- Estadísticas del Sistema -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        <div class="text-center">
            <div class="text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-400" x-text="jugadoras.toLocaleString()"></div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Jugadoras Activas</div>
        </div>
        <div class="text-center">
            <div class="text-2xl md:text-3xl font-bold text-purple-600 dark:text-purple-400" x-text="clubes"></div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Clubes Registrados</div>
        </div>
        <div class="text-center">
            <div class="text-2xl md:text-3xl font-bold text-indigo-600 dark:text-indigo-400" x-text="ligas"></div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Ligas Federadas</div>
        </div>
        <div class="text-center">
            <div class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400" x-text="torneos"></div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Torneos Activos</div>
        </div>
        <div class="text-center">
            <div class="text-2xl md:text-3xl font-bold text-orange-600 dark:text-orange-400" x-text="partidos"></div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Partidos 2024</div>
        </div>
    </div>

    <!-- Estadísticas del Proyecto -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 text-center">Estado del Proyecto VolleyPass</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-emerald-600 dark:text-emerald-400" x-text="completitud + '%'"></div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Completado</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ $tablas }}+</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Tablas BD</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $tests }}+</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Tests</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $uptime }}%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Disponibilidad</div>
            </div>
        </div>
    </div>
</div>
