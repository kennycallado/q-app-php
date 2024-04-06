<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class AuthController extends Render
{
    function login()
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        echo $this->view->render('pages/auth/login.html', ['title' => 'Login', 'error' => $error]);
        return;
    }

    function signup(object $body)
    {
        if (empty($body->username) || empty($body->password) || empty($body->conPassword)) {
            $error = (object) ['code' => '400', 'details' => 'Username, password and email are required'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        // check if password and confirm password are the same
        if ($body->password !== $body->conPassword) {
            $error = (object) ['code' => '400', 'datils' => 'Password and confirm password must be the same'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        $auth = new Auth($_ENV['AUTH_URL']);
        $auth = $auth->signup($body);
        if (isset($auth->error)) {
            $_SESSION['error'] = json_encode($auth->error);

            return header('Location: /login');
        }

        return header('Location: /login');
    }

    function signin(object $body)
    {
        if (empty($body->username) || empty($body->password)) {
            $error = (object) ['code' => '400', 'datils' => 'Username and password are required'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        $auth = new Auth($_ENV['AUTH_URL']);
        $auth = $auth->signin($body);
        if (isset($auth->error)) {
            $_SESSION['error'] = json_encode($auth->error);

            return header('Location: /login');
        }

        // check if user is a parti or guest
        if (in_array($auth->role, ['parti', 'guest'])) {
            $error = (object) ['code' => '400', 'datils' => "You don't have permission to access this site"];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        // check if user is thera and is assigned to a project
        if (!isset($auth->project) && !in_array($auth->role, ['admin', 'coord'])) {
            $error = (object) ['code' => '400', 'details' => 'You are not assigned to any project'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        } elseif (isset($auth->project)) {
            setcookie('project', json_encode($auth->project), time() + (86400 * 30), '/');  // valid for 30 days

            $_SESSION['project'] = $auth->project;
        }

        setcookie('user_id', $auth->user_id, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('gAuth', $auth->gAuth, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('role', $auth->role, time() + (86400 * 30), '/');  // valid for 30 days

        $_SESSION['role'] = $auth->role;

        return header('Location: /');
    }

    function join(Auth $auth, object $body)
    {  // project_id, pass
        if (empty($body->project) || empty($body->pass)) {
            $error = (object) ['code' => '400', 'datils' => 'There are missing fields'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin');
        }

        // check if is trying to join as guest
        if ($body->pass === 'guest') {
            $error = (object) ['code' => '400', 'datils' => 'Is not possible to join as guest'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin');
        }

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);

        $sql = '
            IF ' . $body->project . ' IN (SELECT VALUE out FROM join WHERE in IS ' . $auth->user_id . ') {
                UPDATE ' . $auth->user_id . ' SET project = ' . $body->project . ";
                RETURN SELECT id, name, center.name FROM ONLY $body->project LIMIT 1;
            };";

        $result = $g_surreal->rawQuery($sql);
        if (isset($result->code)) {
            $_SESSION['error'] = json_encode($result);

            return header('Location: /admin');
        }

        $project = $result[0]->result;
        if (empty($project)) {
            $error = (object) ['code' => '400', 'details' => 'You are not allowed to join this project'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin');
        }

        $auth = $auth->join($body->pass, $project->center->name, $project->name);
        if (isset($auth->error)) {
            $_SESSION['error'] = json_encode($auth->error);

            return header('Location: /admin');
        }

        // update cookies and session
        $auth->project = (object) [];
        $auth->project->id = $project->id;
        $auth->project->name = $project->name;
        $auth->project->center = $project->center->name;
        
        setcookie('project', json_encode($auth->project), time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('iAuth', $auth->iAuth, time() + (86400 * 30), '/');  // valid for 30 days

        $_SESSION['project'] = $auth->project;

        return header('Location: /admin');
    }

    function logout()
    {
        // remove session, cookies, etc

        setcookie('gAuth', '', -1, '/');
        setcookie('iAuth', '', -1, '/');
        setcookie('role', '', -1, '/');
        setcookie('user_id', '', -1, '/');
        setcookie('project', '', -1, '/');
        setcookie('center', '', -1, '/');

        session_unset();

        return header('Location: /');
    }
}
