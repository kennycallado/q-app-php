<?php
namespace Src\App\Models;

class Media
{
    private string $id;
    private MediaType $type;
    private string $url;
    private ?string $alt;

    public function __construct(string $id, MediaType|string $type, string $url, ?string $alt)
    {
        $this->id = $id;
        $this->type = $type instanceof MediaType ? $type : MediaType::from($type);
        $this->url = $url;
        $this->alt = $alt;
    }

    public function __get($name)
    {
        if ($name === 'type') {
            return $this->type->name;
        }

        return $this->$name;
    }
}
