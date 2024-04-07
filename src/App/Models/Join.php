<?php
namespace Src\App\Models;

class Join
{
    private string $id;
    private string $in;  // user id
    private string $out;  // project id
    private bool $completed;
    private ?object $score;

    public function __construct(string $id, string $in, string $out, bool $completed = false)
    {
        $this->id = $id;
        $this->in = $in;
        $this->out = $out;
        $this->completed = $completed;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
