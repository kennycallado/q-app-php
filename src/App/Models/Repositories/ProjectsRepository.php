<?php
namespace Src\App\Models\Repositories;

use Src\App\Models\Project;
use Src\Utils\Surreal;

class ProjectsRepository
{
    private Surreal $surreal;

    public function __construct(string $ns, string $db, string $token)
    {
        $this->surreal = new Surreal($ns, $db, $token);
    }

    /**
     * Get all projects
     *
     * @param string $fetch whether to fetch center object or not
     * @return Project[]
     */
    public function all(string $fetch = '')
    {
        if ($fetch !== '') {
            return $this->surreal->select('*')->tables('projects')->fetch($fetch)->exec()[0]->result;
        }

        return $this->surreal->select('*')->tables('projects')->exec()[0]->result;
    }

    /**
     * Get projects by where statement
     *
     * @return Project[]
     */
    public function where($statement, string $fetch = '')
    {
        if ($fetch !== '') {
            return $this->surreal->select('*')->tables('projects')->where($statement)->fetch($fetch)->exec()[0]->result;
        }

        return $this->surreal->select('*')->tables('projects')->where($statement)->exec()[0]->result;
    }

    /**
     * Find project by
     *
     * @param string $column
     * @param string $value
     *
     * @return Project
     */
    public function findBy(string $column, string $value, string $fetch = '')
    {
        if ($fetch !== '') {
            return $this->surreal->select('*')->tables('projects')->where("$column is $value")->fetch($fetch)->exec()[0]->result;
        }

        return $this->surreal->select('*')->tables('projects')->where("$column is $value")->exec()[0]->result;
    }

    public function create(string $name, string $center_name, ?array $keys)
    {
        return $this->surreal->select('*')->tables('projects')->where("$column = $value")->exec()[0]->result;
    }
}
