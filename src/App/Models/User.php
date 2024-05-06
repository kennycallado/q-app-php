<?php
namespace Src\App\Models;

class User
{
    private string $id;
    private Role $role;
    private object $project;
    private string $username;
    private ?string $password;
    private ?object $webtoken;  // notifications

    public function __construct(string $id, Role|string $role, string $username, Project|string $project, ?object $webtoken)
    {
        $this->id = $id;
        $this->role = $role instanceof Role ? $role : Role::from($role);
        $this->project = $project instanceof Project ? $project : (object) ['id' => $project];
        $this->username = $username;
        $this->webtoken = $webtoken;
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
            $this->role = $value instanceof Role ? $value : Role::from($value);
        } elseif ($name === 'project') {
            $this->project = $value instanceof Project ? $value : (object) ['id' => $value];
        }
        
        $this->$name = $value;
    }
}
