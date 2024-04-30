<?php
namespace Src\App\Models;

class Join
{
    private string $id;
    private string $in;  // user id
    private string $out;  // project id
    // private bool $completed;
    private ?string $state;
    private ?object $score;

    public function __construct(string $id, string $in, string $out, ?string $state)
    {
        $this->id = $id;
        $this->in = $in;
        $this->out = $out;
        $this->state = $state;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
