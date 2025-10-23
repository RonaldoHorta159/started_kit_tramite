<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'codigo', 'estado'];

    /** Usuarios que tienen esta área como adicional (pivot area_user) */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'area_user', 'area_id', 'user_id')
            ->withTimestamps();
    }

    /** Usuarios que tienen esta área como principal (users.primary_area_id) */
    public function primaryUsers(): HasMany
    {
        return $this->hasMany(User::class, 'primary_area_id');
    }
}
