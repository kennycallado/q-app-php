<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class AuthController extends Render
{
    private function setCookies(Auth $auth)
    {
        if (isset($auth->project)) {
            setcookie('project', json_encode($auth->project), time() + (86400 * 30), '/');
            $_SESSION['project'] = $auth->project;
        }

        if (isset($auth->pAuth)) {
            setcookie('pAuth', $auth->pAuth, time() + (86400 * 30), '/');
        }

        setcookie('user_id', $auth->user_id, time() + (86400 * 30), '/');
        setcookie('gAuth', $auth->gAuth, time() + (86400 * 30), '/');
        setcookie('role', $auth->role, time() + (86400 * 30), '/');

        $_SESSION['role'] = $auth->role;
        $_SESSION['user_id'] = $auth->user_id;
    }

    function login()
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $prepare = [
            'title' => 'Login',
            'error' => $error
        ];

        echo $this->view->render('pages/auth/login.html', $prepare);
        return;
    }

    function select(array $params)
    {
        $pre_auth = isset($_SESSION['pre_auth']) ? $_SESSION['pre_auth'] : null;
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        if (!$pre_auth) {
            return header('Location: /login');
        }

        if (isset($params['project'])) {
            // update user project
            $surreal = new Surreal('global', 'main', $pre_auth->gAuth);

            $sql = "UPDATE $pre_auth->user_id SET project = $params[project];";

            $response = $surreal->rawQuery($sql);
            if (isset($response->code)) {
                echo '<pre>';
                print_r($response);
                echo '</pre>';

                return;
            }

            // not joinable
            if ($response[0]->status == 'ERR') {
                $error = (object) ['code' => '400', 'details' => 'You are not allowed to join this project'];
                $_SESSION['error'] = json_encode($error);

                return header('Location: /select');
            }

            $auth = new Auth($_ENV['AUTH_URL']);
            $auth = $pre_auth;

            $this::refresh($auth);
        }

        $surreal = new Surreal('global', 'main', $pre_auth->gAuth);

        $sql = 'SELECT *, (SELECT id, name FROM projects WHERE center = $parent.id) AS projects FROM centers;';
        $response = $surreal->rawQuery($sql);
        if (isset($response->code)) {
            echo 'Error: ' . $response->code;
            print_r($response);

            return;
        }

        $centers = $response[0]->result;

        $prepare = [
            'title' => 'Project selection',
            'centers' => $centers,
            'error' => $error
        ];

        echo $this->view->render('pages/auth/select.html', $prepare);
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

            return header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        return header('Location: ' . $_SERVER['HTTP_REFERER']);
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

        if (!isset($auth->role)) {
            $_SESSION['pre_auth'] = $auth;

            return header('Location: /select');
        }

        // check if user is a parti or guest
        if (in_array($auth->role, ['parti', 'guest'])) {
            $error = (object) ['code' => '400', 'datils' => "You don't have permission to access this site"];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        if (isset($auth->project)) {
            setcookie('project', json_encode($auth->project), time() + (86400 * 30), '/');  // valid for 30 days

            $_SESSION['project'] = $auth->project;
        }

        if (isset($auth->pAuth)) {
            setcookie('pAuth', $auth->pAuth, time() + (86400 * 30), '/');  // valid for 30 days
        }

        setcookie('user_id', $auth->user_id, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('gAuth', $auth->gAuth, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('role', $auth->role, time() + (86400 * 30), '/');  // valid for 30 days

        $_SESSION['role'] = $auth->role;
        $_SESSION['user_id'] = $auth->user_id;

        return header('Location: /');
    }

    static function refresh(Auth $auth)
    {
        $auth = $auth->refresh();

        if (!$auth->role || $auth->error) {
            if ($auth->error) {
                $_SESSION['error'] = json_encode($auth->error);
            } else {
                $error = (object) ['code' => '400', 'datils' => "You don't have permission to access this project"];

                $_SESSION['error'] = json_encode($error);
            }

            return header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        if (isset($auth->project)) {
            setcookie('project', json_encode($auth->project), time() + (86400 * 30), '/');  // valid for 30 days

            $_SESSION['project'] = $auth->project;
        }

        if (isset($auth->pAuth)) {
            setcookie('pAuth', $auth->pAuth, time() + (86400 * 30), '/');  // valid for 30 days
        }

        setcookie('user_id', $auth->user_id, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('gAuth', $auth->gAuth, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('role', $auth->role, time() + (86400 * 30), '/');  // valid for 30 days

        $_SESSION['user_id'] = $auth->user_id;
        $_SESSION['role'] = $auth->role;

        if ($_SERVER['REDIRECT_URL'] == '/select') {
            unset($_SESSION['pre_auth']);

            return header('Location: /');
        }

        return header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    function logout()
    {
        // remove session, cookies, etc

        setcookie('gAuth', '', -1, '/');
        setcookie('pAuth', '', -1, '/');
        setcookie('role', '', -1, '/');
        setcookie('user_id', '', -1, '/');
        setcookie('project', '', -1, '/');
        setcookie('center', '', -1, '/');

        session_unset();

        return header('Location: /');
    }
}
