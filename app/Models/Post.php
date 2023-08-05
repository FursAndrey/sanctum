<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'body',
    ];

    protected $with = ['preview'];
    
    public function getPublishedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function preview():HasOne
    {
        return $this->hasOne(Preview::class);
    }

    public function getPreviewUrlAttribute()
    {
        return isset($this->preview)? $this->preview->url: null;
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->nonQueued();
    }
}
