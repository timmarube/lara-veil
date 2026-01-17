<?php

namespace LaraVeil\Posts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Traits\HasMedia;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasMedia;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image_id',
        'status',
        'user_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function featuredImage()
    {
        return $this->belongsTo(\App\Models\Media::class, 'featured_image_id');
    }
}
