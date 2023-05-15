<?php
namespace App\Actions\Preview;

class cutImageIdAction
{
    public function __invoke(array $data): ?int
    {
        $imageId = $data['image_id'];
        unset($data['image_id']);
        return $imageId;
    }
}