<?php
namespace Src\App\Models;

class User
{
  private string $id;
  private Role $role;
  private string $username;
  private $project;
  private ?object $webtoken; // notifications

  public function __construct(string $id, Role|string $role, string $username, ?string $project, ?object $webtoken)
  {
    $this->id = $id;
    $this->role = $role instanceof Role ? $role : Role::from($role);
    $this->username = $username;
    $this->project = $project;
    $this->webtoken = $webtoken;
  }

  public function __get($name)
  {
    return $this->$name;
  }
}
