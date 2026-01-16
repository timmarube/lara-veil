<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent theme.
     */
    public function parent()
    {
        return $this->belongsTo(Theme::class, 'parent_id');
    }

    /**
     * Get child themes.
     */
    public function children()
    {
        return $this->hasMany(Theme::class, 'parent_id');
    }
}
