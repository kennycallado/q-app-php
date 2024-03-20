<?php
namespace Src\App\Models;

class User
{
  private string $id;
  private Role $role;
  private string $username;
  private $project;
  private ?string $password;
  private ?object $webtoken; // notifications

  public function __construct(string $id, Role|string $role, string $username, Project|string $project = null, ?object $webtoken)
  {
    $this->id = $id;
    $this->role = $role instanceof Role ? $role : Role::from($role);
    $this->username = $username;
    $this->project = $project;
    $this->webtoken = $webtoken;
  }

  public function __get($name)
  {
    if ($name === 'role') {
      return $this->role->name;
    }

    return $this->$name;
  }
}
