<?php
namespace Src\App\Models;

enum IntervUserState
{
    case active;
    case exited;
    case standby;
    case completed;

    public static function from(string $value): IntervUserState
    {
        return match ($value) {
            'active' => Self::active,
            'exited' => Self::exited,
            'standby' => Self::standby,
            'completed' => Self::completed,
            default => Self::standby,
        };
    }
}
