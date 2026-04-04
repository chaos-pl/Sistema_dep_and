<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected string $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'acepto_consentimiento',
        'consentimiento_aceptado_at',
        'avatar_icon',
        'appearance_settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'acepto_consentimiento' => 'boolean',
            'consentimiento_aceptado_at' => 'datetime',
            'appearance_settings' => 'array',
        ];
    }

    public function persona()
    {
        return $this->hasOne(Persona::class);
    }

    /**
     * Accesor para obtener la configuración de apariencia
     * con valores por defecto.
     */
    public function getAppearanceAttribute(): array
    {
        return array_merge([
            'theme' => 'light',
            'accent_color' => 'purple',
            'density' => 'comfortable',
            'reduced_motion' => false,
        ], $this->appearance_settings ?? []);
    }

    /**
     * Accesor para obtener la clase Bootstrap Icons
     * correspondiente al icono seleccionado.
     */
    public function getAvatarIconClassAttribute(): string
    {
        return config(
            'appearance.avatar_icons.' . $this->avatar_icon . '.class',
            'bi bi-person-circle'
        );
    }
    public function estudiante()
    {
        return $this->hasOneThrough(
            \App\Models\Estudiante::class,
            \App\Models\Persona::class,
            'user_id',     // Foreign key en personas
            'persona_id',  // Foreign key en estudiantes
            'id',          // Local key en users
            'id'           // Local key en personas
        );
    }
}
