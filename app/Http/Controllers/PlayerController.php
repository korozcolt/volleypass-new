<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user->player) {
            return redirect()->route('home');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'birth_date',
            'address'
        ]));

        return redirect()->route('player.profile')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        if (!$user->player) {
            return redirect()->route('home');
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($user->getFirstMedia('avatar')) {
                $user->getFirstMedia('avatar')->delete();
            }

            // Subir nueva foto
            $user->addMediaFromRequest('photo')
                ->toMediaCollection('avatar');
        }

        return redirect()->route('player.profile')
            ->with('success', 'Foto de perfil actualizada correctamente.');
    }

    public function downloadCard()
    {
        $user = Auth::user();

        if (!$user->player) {
            return redirect()->route('home');
        }

        // Aquí implementarías la lógica para generar y descargar el carnet digital
        // Por ejemplo, usando una librería como DomPDF o similar

        return response()->json([
            'message' => 'Funcionalidad de descarga de carnet en desarrollo'
        ]);
    }
}
