<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nom', 'description', 'deadline', 'archive', 'status_id', 'creator_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function membres()
    {
        return $this->belongsToMany(User::class, 'membre_projets', 'projet_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }
}