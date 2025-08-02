<?php

namespace App\Filament\Resources\RefereeResource\Pages;

use App\Filament\Resources\RefereeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CreateReferee extends CreateRecord
{
    protected static string $resource = RefereeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the creator
        $data['created_by'] = Auth::user()?->id;
        
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // If creating a new user along with the referee
        if (isset($data['user_id']) && is_array($data['user_id'])) {
            $userData = $data['user_id'];
            $userData['password'] = Hash::make($userData['password']);
            
            $user = User::create($userData);
            $user->assignRole('Referee');
            
            $data['user_id'] = $user->id;
        }

        return static::getModel()::create($data);
    }
}