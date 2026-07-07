<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class Admin extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'admin';

    // Called by Filament on every login attempt and on each authenticated
    // request; disabling an admin (is_active=false) locks them out immediately
    // without needing to delete the account or revoke a session manually.
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasVerifiedEmail();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
