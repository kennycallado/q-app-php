<?php
namespace Src\App\Controllers\Entities;

use Src\App\Models\Repositories\UsersRepository;
use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class UsersController extends Render
{
    public function parti(Auth $auth)
    {
        // check if user has been joined the project
        if (!isset($auth->iAuth)) {
            $error = (object) ['code' => '400', 'details' => 'You have not joined the project yet.'];

            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin');
        }

        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->iAuth);
        $u_repo = new UsersRepository('global', 'main', $auth->gAuth);
        $users = [];

        $sql = 'SELECT * FROM users;';
        $i_users = $i_surreal->rawQuery($sql);
        if (isset($i_users->code)) {
            echo 'Error: ' . $i_users->code;
            print_r($i_users);

            return;
        } else {
            $i_users = $i_users[0]->result;
        }

        $users = $u_repo->where('project = ' . $auth->project->id . " AND role == 'parti'");

        // map $users to add active from i_users
        array_map(function ($user) use ($i_users) {
            foreach ($i_users as $i_user) {
                if ($i_user->id == $user->id) {
                    $user->active = $i_user->active;
                    break;
                }
            }
        }, $users);

        echo $this->view->render('pages/admin/users/parti.html', ['title' => 'Users', 'users' => $users]);
        return;
    }

    public function permissions(Auth $auth)
    {
        if (!in_array($auth->role, ['admin', 'coord'])) {
            header('Location: /admin');

            return;
        }

        $repo = new UsersRepository('global', 'main', $auth->gAuth);
        $users = $repo->where('project = ' . $auth->project->id . " AND role != 'parti'");

        echo $this->view->render('pages/admin/users/permissions.html', ['title' => 'Users', 'users' => $users]);
        return;
    }

    public function show(Auth $auth, array $params)
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $g_surreal = new Surreal('global', 'amin', $auth->gAuth);
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->iAuth);

        $u_repo = new UsersRepository('global', 'main', $auth->gAuth);

        // get project keys
        $res = $g_surreal->rawQuery('SELECT keys FROM ' . $auth->project->id . ' ;');
        $p_keys = $res[0]->result ?? [];

        // get user
        $user = $u_repo->findBy('id', $params['id'])[0];

        // get user active and scores
        $sql = "SELECT VALUE active FROM ONLY $user->id LIMIT 1;";
        $sql .= "SELECT * FROM scores WHERE user = $user->id ORDER BY created DESC;";

        $res = $i_surreal->rawQuery($sql);
        if (isset($res->code)) {
            echo 'Error: ' . $res->code;
            print_r($res);

            return;
        }

        $num_rows = count($res);

        $scores = $res[--$num_rows]->result;
        $active = $res[--$num_rows]->result ?? [];

        $user->active = $active ?? false;
        $user->scores = $scores;

        echo $this->view->render('pages/admin/users/details.html', ['title' => 'Details', 'p_keys' => $p_keys, 'user' => $user, 'error' => $error]);
        return;
    }
}
