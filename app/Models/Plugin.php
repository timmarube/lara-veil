<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'name',
        'namespace',
        'version',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Check if the plugin is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
