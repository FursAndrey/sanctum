<?php
namespace App\Actions\Preview;

use App\Models\Preview;

class destroyAllUnjoinedPreviews
{
    public function __invoke():void
    {
        $preview = Preview::where('user_id', '=', auth()->id())->whereNull('post_id')->get();
        foreach ($preview as $preview) {
            (new destroyOnePreview)($preview);
        }
    }
}