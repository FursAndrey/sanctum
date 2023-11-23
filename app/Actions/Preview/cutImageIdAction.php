<?php

namespace App\Actions\Preview;

class cutImageIdAction
{
    public function __invoke(array $data): ?int
    {
        if (isset($data['image_id'])) {
            $imageId = $data['image_id'];
            unset($data['image_id']);
        } else {
            $imageId = null;
        }

        return $imageId;
    }
}
