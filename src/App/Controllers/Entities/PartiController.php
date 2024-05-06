<?php
namespace Src\App\Controllers\Entities;

use Src\App\Models\Repositories\UsersRepository;
use Src\App\Models\IntervUser;
use Src\App\Models\User;
use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class PartiController extends Render
{
    public function store(Auth $auth, object $body, array $params)
    {
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $user = (object) [
            'id' => $params['id'],
            'role' => $body->role ?? 'parti',
            'state' => $body->state,
            'project' => $body->project,
            'username' => $body->username,
        ];

        $sql = "UPDATE $user->id MERGE " . json_encode($user) . ";";
        $res = $i_surreal->rawQuery($sql);
        if (isset($res->code)) {
            echo 'Error: ' . $res->code;
            print_r($res);

            return;
        }

        return header('Location: /admin/parti/' . $params['id']);
    }

    public function assign_user(Auth $auth, object $body) {
        $username = $body->username;

        $u_repo = new UsersRepository('global', 'main', $auth->gAuth);
        $res = $u_repo->where("username = '$username'");

        if (count($res) == 0) {
            $error = (object) ['code' => '400', 'details' => 'User does not exist.'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin/parti');
        } else {
            $user = $res[0];
        }

        if (!isset($user)) {
            $error = (object) ['code' => '400', 'details' => 'User does not exist.'];
            $_SESSION['error'] = json_encode($error);
        } elseif ($user->role != 'parti') {
            $error = (object) ['code' => '400', 'details' => 'User is not a participant.'];
            $_SESSION['error'] = json_encode($error);
        } elseif (isset($user->project)) {
            $error = (object) ['code' => '400', 'details' => 'User is already assigned to a project.'];
            $_SESSION['error'] = json_encode($error);
        } else {
            $user->project = $auth->project->id;
            $user = $u_repo->update($user);


            // $error = (object) ['code' => '200', 'details' => 'User has been assigned to the project.'];
            // $_SESSION['error'] = json_encode($error);
        }

        return header('Location: /admin/parti');
    }

    public function index(Auth $auth)
    {
        // check if user has been joined the project
        if (!isset($auth->pAuth)) {
            $error = (object) ['code' => '400', 'details' => 'You have not joined the project yet.'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /user/settings');
        }

        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $users = [];

        $sql = 'SELECT * FROM users;';
        /** @var array | object $i_users */
        $i_users = $i_surreal->rawQuery($sql);
        if (isset($i_users->code)) {
            echo 'Error: ' . $i_users->code;
            print_r($i_users);

            return;
        } else {
            /** @var IntervUser[] $i_users */
            $i_users = $i_users[0]->result;
        }

        $sql = 'SELECT VALUE in FROM join WHERE out IS ' . $auth->project->id . " AND in.role IS 'parti' FETCH in;";
        $users = $g_surreal->rawQuery($sql);
        if (isset($users->code)) {
            echo 'Error: ' . $users->code;
            print_r($users);

            return;
        } else {
            $users = $users[0]->result;
        }

        // map $users to add active from i_users
        array_map(function (mixed $user) use ($i_users) {
            foreach ($i_users as $i_user) {
                if ($i_user->id == $user->id) {
                    $user->state = $i_user->state;

                    break;
                }
            }
        }, $users);

        $prepare = [
            'title' => 'Participants',
            'error' => $error,
            'users' => $users
        ];

        echo $this->view->render('pages/admin/participants/index.html', $prepare);
        return;
    }

    public function show(Auth $auth, array $params)
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $g_surreal = new Surreal('global', 'amin', $auth->gAuth);
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $u_repo = new UsersRepository('global', 'main', $auth->gAuth);

        // get project keys
        $res = $g_surreal->rawQuery('SELECT keys FROM ' . $auth->project->id . ' ;');
        $p_keys = $res[0]->result ?? [];

        // get user
        $user = $u_repo->findBy('id', $params['id'])[0];

        // get user active and scores
        $sql = "SELECT state FROM ONLY $user->id LIMIT 1;";
        $sql .= "SELECT * FROM scores WHERE user = $user->id ORDER BY created DESC;";
        $sql .= "SELECT id, created, completed, resource.ref, resource.id FROM papers WHERE user = $user->id ORDER BY created DESC;";

        $res = $i_surreal->rawQuery($sql);
        if (isset($res->code)) {
            echo 'Error: ' . $res->code;
            print_r($res);

            return;
        }

        $num_rows = count($res);

        $papers = $res[--$num_rows]->result;
        $scores = $res[--$num_rows]->result;
        $u_state = $res[--$num_rows]->result;

        /** @var mixed $user */
        $user->state = $u_state->state ?? false;
        $user->scores = $scores ?? [];
        $user->papers = $papers ?? [];

        $prepare = [
            'title' => 'Details',
            'error' => $error,
            'p_keys' => $p_keys,
            'user' => $user
        ];

        echo $this->view->render('pages/admin/participants/details.html', $prepare);
        return;
    }
}
