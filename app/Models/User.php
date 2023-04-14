<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nom', 'prenom', 'matricule', 'bio', 'avatar', 'active', 'email', 'password', 'role_id'];

    public function projets()
    {
        return $this->hasMany(Projet::class, 'creator_id');
    }

    public function projet()
    {
        return $this->belongsToMany(Projet::class, 'membre_projets', 'user_id', 'projet_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ]; 

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get default avatar for user
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        return $value ?
            // $value 
            Storage::url($value)
            : 'https://eu.ui-avatars.com/api/?font-size=0.6&bold=true&size=300&background=101213&color=FFFFFF&name=' . $this->attributes['email'];
    }

    /**
     * Always encrypt the password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
