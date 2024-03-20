<?php
namespace Src\App\Models;

class Project
{
    private string $id;
    private $center;
    private ProjectState $state;
    private string $token;
    private string $name;
    private ?object $settings;

    public function __construct(string $id, string $name, Center|string $center, ProjectState|string $state, string $token, ?object $settings)
    {
        $this->id = $id;
        $this->name = $name;
        $this->center = $center;
        $this->state = $state instanceof ProjectState ? $state : ProjectState::from($state);
        $this->token = $token;
        $this->settings = $settings;
    }

    public function __get($name)
    {
        if ($name === 'state') {
            return $this->state->name;
        }

        return $this->$name;
    }
}
