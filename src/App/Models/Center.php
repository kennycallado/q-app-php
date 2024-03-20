<?php
namespace Src\App\Models;

class Center
{
    private string $id;
    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
