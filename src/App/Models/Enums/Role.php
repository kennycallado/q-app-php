<?php
namespace Src\App\Models;

enum Role
{
    case admin;
    case coord;
    case thera;
    case parti;
    case guest;

    public static function from(string $role): Role
    {
        return match ($role) {
            'admin' => Role::admin,
            'coord' => Role::coord,
            'thera' => Role::thera,
            'parti' => Role::parti,
            'guest' => Role::guest,
            default => Role::parti,
        };
    }
}
