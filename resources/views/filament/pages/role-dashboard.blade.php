<x-filament-panels::page>
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        $data = $this->getRoleSpecificData();
    @endphp

    <div class="space-y-6">
        <!-- Bienvenida personalizada por rol -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg p-6 text-white">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @switch($role)
                        @case('SuperAdmin')
                            <x-heroicon-o-shield-check class="h-12 w-12" />
                            @break
                        @case('LeagueAdmin')
                            <x-heroicon-o-trophy class="h-12 w-12" />
                            @break
                        @case('ClubDirector')
                            <x-heroicon-o-building-office class="h-12 w-12" />
                            @break
                        @case('Coach')
                            <x-heroicon-o-user-group class="h-12 w-12" />
                            @break
                        @case('SportsDoctor')
                            <x-heroicon-o-heart class="h-12 w-12" />
                            @break
                        @case('Verifier')
                            <x-heroicon-o-document-check class="h-12 w-12" />
                            @break
                        @default
                            <x-heroicon-o-user class="h-12 w-12" />
                    @endswitch
                </div>
                <div>
                    <h1 class="text-2xl font-bold">¡Bienvenido, {{ $user->name }}!</h1>
                    <p class="text-primary-100">
                        @switch($role)
                            @case('SuperAdmin')
                                Administrador del Sistema VolleyPass
                                @break
                            @case('LeagueAdmin')
                                Administrador de Liga
                                @break
                            @case('ClubDirector')
                                Director de Club
                                @break
                            @case('Coach')
                                Entrenador
                                @break
                            @case('SportsDoctor')
                                Médico Deportivo
                                @break
                            @case('Verifier')
                                Verificador de Documentos
                                @break
                            @default
                                Usuario del Sistema
                        @endswitch
                    </p>
                </div>
            </div>
        </div>

        <!-- Estadísticas específicas por rol -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @switch($role)
                @case('SuperAdmin')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-users class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Usuarios</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalUsers'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-building-office class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Clubes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalClubs'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-trophy class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Torneos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalTournaments'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-user-group class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Jugadores</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalPlayers'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                @case('LeagueAdmin')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-trophy class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Torneos Activos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['activeTournaments'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-building-office class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Clubes Registrados</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalClubs'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-user-group class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Equipos Activos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalTeams'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-calendar class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Torneos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalTournaments'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                @case('ClubDirector')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-user-group class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Mis Equipos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['activeTeams'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Jugadores</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalPlayers'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-credit-card class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pagos Pendientes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['pendingPayments'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-building-office class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Mi Club</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $data['myClub']->name ?? 'Sin asignar' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                @case('Coach')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-user-group class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Mis Equipos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $data['myTeams']->count() ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Jugadores</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalPlayers'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-calendar class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Próximos Partidos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $data['upcomingMatches']->count() ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-clock class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Entrenamientos</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $data['trainingSchedule']->count() ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                @case('SportsDoctor')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-users class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Pacientes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalPatients'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-document-text class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Certificados Pendientes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['pendingCertificates'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Por Vencer (30 días)</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['expiringCertificates'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-heart class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Certificados Recientes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $data['recentCertificates']->count() ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break

                @case('Verifier')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Verificaciones Pendientes</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['pendingVerifications'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Verificadas Hoy</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['verifiedToday'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-shield-check class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Verificadas</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['totalVerified'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-x-circle class="h-6 w-6 text-red-600 dark:text-red-400" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Documentos Rechazados</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($data['rejectedDocuments'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break
            @endswitch
        </div>

        <!-- Acciones rápidas por rol -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Acciones Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @switch($role)
                    @case('SuperAdmin')
                        <a href="{{ route('filament.admin.resources.usuarios.index') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <x-heroicon-o-users class="h-8 w-8 text-blue-600 dark:text-blue-400 mr-3" />
                            <div>
                                <div class="font-medium text-blue-900 dark:text-blue-100">Gestionar Usuarios</div>
                                <div class="text-sm text-blue-600 dark:text-blue-300">Administrar cuentas de usuario</div>
                            </div>
                        </a>
                        <a href="{{ route('filament.admin.resources.system-configurations.index') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <x-heroicon-o-cog-6-tooth class="h-8 w-8 text-green-600 dark:text-green-400 mr-3" />
                            <div>
                                <div class="font-medium text-green-900 dark:text-green-100">Configuración</div>
                                <div class="text-sm text-green-600 dark:text-green-300">Gestionar configuraciones del sistema</div>
                            </div>
                        </a>

                        <a href="{{ route('filament.admin.resources.tournaments.index') }}" class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                            <x-heroicon-o-trophy class="h-8 w-8 text-yellow-600 dark:text-yellow-400 mr-3" />
                            <div>
                                <div class="font-medium text-yellow-900 dark:text-yellow-100">Torneos</div>
                                <div class="text-sm text-yellow-600 dark:text-yellow-300">Gestionar torneos</div>
                            </div>
                        </a>

                        <a href="{{ route('filament.admin.resources.teams.index') }}" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <x-heroicon-o-user-group class="h-8 w-8 text-purple-600 dark:text-purple-400 mr-3" />
                            <div>
                                <div class="font-medium text-purple-900 dark:text-purple-100">Equipos</div>
                                <div class="text-sm text-purple-600 dark:text-purple-300">Gestionar equipos</div>
                            </div>
                        </a>
                        @break

                    @case('LeagueAdmin')
                        <a href="{{ route('filament.admin.resources.tournaments.index') }}" class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                            <x-heroicon-o-trophy class="h-8 w-8 text-yellow-600 dark:text-yellow-400 mr-3" />
                            <div>
                                <div class="font-medium text-yellow-900 dark:text-yellow-100">Gestionar Torneos</div>
                                <div class="text-sm text-yellow-600 dark:text-yellow-300">Crear y administrar torneos</div>
                            </div>
                        </a>
                        @break

                    @case('ClubDirector')
                        <a href="{{ route('filament.admin.resources.teams.index') }}" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <x-heroicon-o-user-group class="h-8 w-8 text-purple-600 dark:text-purple-400 mr-3" />
                            <div>
                                <div class="font-medium text-purple-900 dark:text-purple-100">Mis Equipos</div>
                                <div class="text-sm text-purple-600 dark:text-purple-300">Gestionar equipos del club</div>
                            </div>
                        </a>
                        @break

                    @case('Coach')
                        <a href="{{ route('filament.admin.resources.players.index') }}" class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                            <x-heroicon-o-users class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mr-3" />
                            <div>
                                <div class="font-medium text-indigo-900 dark:text-indigo-100">Mis Jugadores</div>
                                <div class="text-sm text-indigo-600 dark:text-indigo-300">Ver y gestionar jugadores</div>
                            </div>
                        </a>
                        @break

                    @case('SportsDoctor')
                        <a href="{{ route('filament.admin.resources.medical-certificate.index') }}" class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            <x-heroicon-o-document-check class="h-8 w-8 text-red-600 dark:text-red-400 mr-3" />
                            <div>
                                <div class="font-medium text-red-900 dark:text-red-100">Certificados Médicos</div>
                                <div class="text-sm text-red-600 dark:text-red-300">Gestionar certificaciones</div>
                            </div>
                        </a>
                        @break

                    @case('Verifier')
                        <a href="{{ route('filament.admin.resources.player-card.index') }}" class="flex items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors">
                            <x-heroicon-o-document-check class="h-8 w-8 text-teal-600 dark:text-teal-400 mr-3" />
                            <div>
                                <div class="font-medium text-teal-900 dark:text-teal-100">Verificar Documentos</div>
                                <div class="text-sm text-teal-600 dark:text-teal-300">Revisar y aprobar documentos</div>
                            </div>
                        </a>
                        @break
                @endswitch
            </div>
        </div>
    </div>
</x-filament-panels::page>