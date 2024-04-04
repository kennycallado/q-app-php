<?php
namespace Src\App\Models;

class Project
{
    private string $id;
    private string $name;
    private object $center;
    private ProjectState $state;
    private string $token;
    private ?object $settings;
    private ?array $keys;

    public function __construct(string $id, string $name, Center|string $center, ProjectState|string $state, string $token, ?array $keys, ?object $settings)
    {
        $this->id = $id;
        $this->name = $name;
        $this->center = $center instanceof Center ? $center : (object) ['id' => $center];
        $this->state = $state instanceof ProjectState ? $state : ProjectState::from($state);
        $this->token = $token;
        $this->keys = $keys;
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
