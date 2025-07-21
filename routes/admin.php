<?php

use App\Http\Controllers\Admin\LeagueCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Rutas para la administración de categorías de liga
|
*/

Route::middleware(['web'])->prefix('admin')->group(function () {

    // Rutas para gestión de categorías de liga
    Route::prefix('leagues/{league}')->group(function () {
        Route::prefix('categories')->group(function () {
            // Crear categorías por defecto
            Route::post('create-default', [LeagueCategoryController::class, 'createDefault'])
                ->name('admin.leagues.categories.create-default');

            // Validar configuración
            Route::post('validate', [LeagueCategoryController::class, 'validate'])
                ->name('admin.leagues.categories.validate');

            // Exportar configuración
            Route::get('export', [LeagueCategoryController::class, 'export'])
                ->name('admin.leagues.categories.export');

            // Crear nueva categoría
            Route::post('/', [LeagueCategoryController::class, 'store'])
                ->name('admin.leagues.categories.store');
        });
    });

    // Rutas para gestión individual de categorías
    Route::prefix('categories/{category}')->group(function () {
        // Obtener información de categoría
        Route::get('/', [LeagueCategoryController::class, 'show'])
            ->name('admin.categories.show');

        // Actualizar categoría
        Route::put('/', [LeagueCategoryController::class, 'update'])
            ->name('admin.categories.update');

        // Cambiar estado activo/inactivo
        Route::post('toggle-status', [LeagueCategoryController::class, 'toggleStatus'])
            ->name('admin.categories.toggle-status');

        // Eliminar categoría
        Route::delete('/', [LeagueCategoryController::class, 'destroy'])
            ->name('admin.categories.destroy');
    });

});
