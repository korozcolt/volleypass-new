<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Información Personal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Información Personal</h3>
                    
                    <form wire:submit.prevent="updatePersonalInfo">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Foto de Perfil -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Foto de Perfil
                                </label>
                                <div class="flex items-center space-x-6">
                                    @if($user->profile_photo_path)
                                        <img class="h-20 w-20 rounded-full object-cover" 
                                             src="{{ Storage::url($user->profile_photo_path) }}" 
                                             alt="{{ $user->name }}">
                                    @else
                                        <div class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-2xl font-medium text-gray-700 dark:text-gray-300">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <input type="file" wire:model="photo" accept="image/*" class="hidden" id="photo-upload">
                                        <label for="photo-upload" 
                                               class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                            Cambiar Foto
                                        </label>
                                        @if($user->profile_photo_path)
                                            <button type="button" wire:click="deletePhoto" 
                                                    class="ml-2 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre Completo
                                </label>
                                <input type="text" id="name" wire:model="name" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Correo Electrónico
                                </label>
                                <input type="email" id="email" wire:model="email" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Teléfono
                                </label>
                                <input type="tel" id="phone" wire:model="phone" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Documento de Identidad -->
                            <div>
                                <label for="document_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Documento de Identidad
                                </label>
                                <input type="text" id="document_number" wire:model="document_number" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('document_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información Deportiva -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Información Deportiva</h3>
                    
                    <form wire:submit.prevent="updateSportsInfo">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Posición -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Posición
                                </label>
                                <select id="position" wire:model="position" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Seleccionar posición</option>
                                    <option value="Armadora">Armadora</option>
                                    <option value="Opuesta">Opuesta</option>
                                    <option value="Central">Central</option>
                                    <option value="Punta">Punta</option>
                                    <option value="Líbero">Líbero</option>
                                </select>
                                @error('position') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Número de Camiseta -->
                            <div>
                                <label for="jersey_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Número de Camiseta
                                </label>
                                <input type="number" id="jersey_number" wire:model="jersey_number" min="1" max="99"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('jersey_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Altura -->
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Altura (cm)
                                </label>
                                <input type="number" id="height" wire:model="height" min="140" max="220"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('height') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Peso -->
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Peso (kg)
                                </label>
                                <input type="number" id="weight" wire:model="weight" min="40" max="120"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('weight') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fecha de Nacimiento
                                </label>
                                <input type="date" id="birth_date" wire:model="birth_date" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('birth_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Años de Experiencia -->
                            <div>
                                <label for="years_experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Años de Experiencia
                                </label>
                                <input type="number" id="years_experience" wire:model="years_experience" min="0" max="30"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('years_experience') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Información Deportiva
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información Médica -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Información Médica</h3>
                    
                    <form wire:submit.prevent="updateMedicalInfo">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Sangre -->
                            <div>
                                <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo de Sangre
                                </label>
                                <select id="blood_type" wire:model="blood_type" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                                @error('blood_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contacto de Emergencia -->
                            <div>
                                <label for="emergency_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contacto de Emergencia
                                </label>
                                <input type="text" id="emergency_contact" wire:model="emergency_contact" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('emergency_contact') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Teléfono de Emergencia -->
                            <div>
                                <label for="emergency_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Teléfono de Emergencia
                                </label>
                                <input type="tel" id="emergency_phone" wire:model="emergency_phone" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('emergency_phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Alergias -->
                            <div class="md:col-span-2">
                                <label for="allergies" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Alergias
                                </label>
                                <textarea id="allergies" wire:model="allergies" rows="3"
                                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          placeholder="Describe cualquier alergia conocida..."></textarea>
                                @error('allergies') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Condiciones Médicas -->
                            <div class="md:col-span-2">
                                <label for="medical_conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Condiciones Médicas
                                </label>
                                <textarea id="medical_conditions" wire:model="medical_conditions" rows="3"
                                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          placeholder="Describe cualquier condición médica relevante..."></textarea>
                                @error('medical_conditions') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Guardar Información Médica
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Cambiar Contraseña</h3>
                    
                    <form wire:submit.prevent="updatePassword">
                        <div class="grid grid-cols-1 gap-6 max-w-md">
                            <!-- Contraseña Actual -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contraseña Actual
                                </label>
                                <input type="password" id="current_password" wire:model="current_password" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('current_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nueva Contraseña -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nueva Contraseña
                                </label>
                                <input type="password" id="password" wire:model="password" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" id="password_confirmation" wire:model="password_confirmation" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('password_confirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>