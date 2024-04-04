<?php
namespace Src\App\Models\Repositories;

use Src\App\Models\User;
use Src\Utils\Surreal;

class UsersRepository
{
    private Surreal $surreal;

    public function __construct(string $ns, string $db, string $token)
    {
        $this->surreal = new Surreal($ns, $db, $token);
    }

    /**
     * Get all users
     *
     * @return User[]
     */
    public function all()
    {
        return $this->surreal->select('*')->tables('users')->exec()[0]->result;
    }

    /**
     * Get all users
     *
     * @return User[]
     */
    public function where($statement)
    {
        return $this->surreal->select('*')->tables('users')->where($statement)->exec()[0]->result;
    }

    /**
     * Find user by id
     *
     * @param int $id
     * @return User[]
     */
    public function findBy(string $column, string $value)
    {
        return $this->surreal->select('*')->tables('users')->where("$column = $value")->exec()[0]->result;
    }
}
