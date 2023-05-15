<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

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
}
