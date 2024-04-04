<?php
namespace Src\App\Models;

enum MediaType
{
    case image;
    case video;

    public static function from(string $value): self
    {
        return match ($value) {
            'image' => Self::image,
            'video' => Self::video,
        };
    }
}
