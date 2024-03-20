<?php
namespace Src\App\Models\Repositories;

use Src\Utils\Surreal;

class UserRepository
{
    private Surreal $surreal;

    public function __construct(string $ns, string $db, string $token)
    {
        $this->surreal = new Surreal($ns, $db, $token);
    }

    /**
     * Get all users
     *
     * @return array
     */
    public function all()
    {
        return $this->surreal->select('*')->tables('users')->exec();
    }
}
