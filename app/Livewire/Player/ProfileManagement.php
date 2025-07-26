<?php

namespace App\Livewire\Player;

use App\Models\User;
use App\Models\UserProfile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.player-dashboard')]
class ProfileManagement extends Component
{
    use WithFileUploads;

    // Datos del usuario
    #[Validate('required|string|max:255')]
    public $name;
    
    #[Validate('required|email|unique:users,email')]
    public $email;
    
    #[Validate('nullable|string|max:20')]
    public $phone;
    
    #[Validate('nullable|string|max:500')]
    public $address;
    
    // Información de emergencia
    #[Validate('nullable|string|max:255')]
    public $emergency_contact_name;
    
    #[Validate('nullable|string|max:20')]
    public $emergency_contact_phone;
    
    #[Validate('nullable|string|max:100')]
    public $emergency_contact_relationship;
    
    // Foto de perfil
    #[Validate('nullable|image|max:2048')] // 2MB máximo
    public $profile_photo;
    
    public $current_profile_photo;
    
    // Cambio de contraseña
    #[Validate('required|current_password')]
    public $current_password;
    
    public $new_password;
    
    #[Validate('required|same:new_password')]
    public $new_password_confirmation;
    
    // Preferencias de notificaciones
    public $email_notifications = true;
    public $whatsapp_notifications = false;
    public $push_notifications = true;
    
    // Configuraciones de privacidad
    public $profile_visibility = 'public'; // public, team_only, private
    public $stats_visibility = 'public';
    
    public $showPasswordForm = false;
    public $showNotificationSettings = false;
    
    public function mount()
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $profile->phone ?? '';
        $this->address = $profile->address ?? '';
        
        $this->emergency_contact_name = $profile->emergency_contact_name ?? '';
        $this->emergency_contact_phone = $profile->emergency_contact_phone ?? '';
        $this->emergency_contact_relationship = $profile->emergency_contact_relationship ?? '';
        
        $this->current_profile_photo = $profile->profile_photo_url;
        
        $this->email_notifications = $profile->email_notifications ?? true;
        $this->whatsapp_notifications = $profile->whatsapp_notifications ?? false;
        $this->push_notifications = $profile->push_notifications ?? true;
        
        $this->profile_visibility = $profile->profile_visibility ?? 'public';
        $this->stats_visibility = $profile->stats_visibility ?? 'public';
    }
    
    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);
        
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        // Actualizar datos del usuario
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);
        
        // Actualizar perfil
        $profile->fill([
            'phone' => $this->phone,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'emergency_contact_relationship' => $this->emergency_contact_relationship,
        ]);
        
        $profile->save();
        
        $this->dispatch('profile-updated', [
            'message' => 'Perfil actualizado correctamente'
        ]);
    }
    
    public function updateProfilePhoto()
    {
        $this->validate([
            'profile_photo' => 'required|image|max:2048'
        ]);
        
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        // Eliminar foto anterior si existe
        if ($profile->profile_photo_path) {
            Storage::disk('public')->delete($profile->profile_photo_path);
        }
        
        // Guardar nueva foto
        $path = $this->profile_photo->store('profile-photos', 'public');
        
        $profile->profile_photo_path = $path;
        $profile->save();
        
        $this->current_profile_photo = Storage::url($path);
        $this->profile_photo = null;
        
        $this->dispatch('photo-updated', [
            'message' => 'Foto de perfil actualizada correctamente'
        ]);
    }
    
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', Password::defaults()],
            'new_password_confirmation' => 'required|same:new_password',
        ]);
        
        Auth::user()->update([
            'password' => Hash::make($this->new_password)
        ]);
        
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->showPasswordForm = false;
        
        $this->dispatch('password-updated', [
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
    
    public function updateNotificationSettings()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        $profile->fill([
            'email_notifications' => $this->email_notifications,
            'whatsapp_notifications' => $this->whatsapp_notifications,
            'push_notifications' => $this->push_notifications,
        ]);
        
        $profile->save();
        
        $this->dispatch('notifications-updated', [
            'message' => 'Preferencias de notificaciones actualizadas'
        ]);
    }
    
    public function updatePrivacySettings()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        $profile->fill([
            'profile_visibility' => $this->profile_visibility,
            'stats_visibility' => $this->stats_visibility,
        ]);
        
        $profile->save();
        
        $this->dispatch('privacy-updated', [
            'message' => 'Configuraciones de privacidad actualizadas'
        ]);
    }
    
    public function togglePasswordForm()
    {
        $this->showPasswordForm = !$this->showPasswordForm;
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }
    
    public function toggleNotificationSettings()
    {
        $this->showNotificationSettings = !$this->showNotificationSettings;
    }
    
    public function render()
    {
        return view('livewire.player.profile-management');
    }
}