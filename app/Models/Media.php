<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $guarded = [];

    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'generated_conversions' => 'array',
        'responsive_images' => 'array',
    ];

    public function model()
    {
        return $this->morphTo();
    }
    
    public function getUrlAttribute()
    {
        // If the file exists in public directory directly (checking relative to public root)
        if ($this->disk === 'public' && file_exists(public_path($this->path))) {
            return asset($this->path);
        }
        
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->path);
    }
}
