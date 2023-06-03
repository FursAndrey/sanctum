<?php
namespace App\Actions\Preview;

use App\Models\Preview;
use Illuminate\Support\Facades\Storage;

class destroyOnePreviewAction
{
    public function __invoke(Preview $preview):void
    {
        $preview->delete();
        Storage::disk('public')->delete($preview->path);
    }
}