<?php

namespace App\Traits;

use App\Models\Media;
use App\Services\Vrm\MediaForgeService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    /**
     * Get all media for this model.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Get media from a specific collection.
     */
    public function getMedia(string $collection = 'default')
    {
        return $this->media()->where('collection_name', $collection)->get();
    }

    /**
     * Get the first media url from a collection.
     */
    public function getFirstMediaUrl(string $collection = 'default', string $conversion = ''): string
    {
        $media = $this->media()->where('collection_name', $collection)->first();

        if (!$media) {
            return '';
        }

        return $media->url;
    }

    /**
     * Add media to the model.
     * 
     * @param mixed $file UploadedFile or path
     * @return MediaForgeService
     */
    public function addMedia($file): MediaForgeService
    {
        $service = new MediaForgeService();
        
        if (is_string($file) && filter_var($file, FILTER_VALIDATE_URL)) {
             $service->uploadFromUrl($file);
        } else {
             $service->upload($file);
        }
        
        return $service->forModel($this);
    }
}
