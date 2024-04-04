<?php
namespace Src\App\Models;

class IntervUser
{
    private string $id;
    private Role $role;
    private bool $active;
    private bool $completed;
    private array $scores;

    public function __construct(string $id, Role|string $role, bool $active = false, bool $completed = false)
    {
        $this->id = $id;
        $this->role = $role instanceof Role ? $role : Role::from($role);
        $this->active = $active;
        $this->completed = $completed;
    }

    public function __get($name)
    {
        if ($name === 'role') {
            return $this->role->name;
        }

        return $this->$name;
    }

    public function __set($name, $value)
    {
        if ($name === 'role') {
            $this->role = Role::from($value);
        } else {
            $this->$name = $value;
        }
    }
}
