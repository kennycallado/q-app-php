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
            if ($body->password !== $body->conPassword) {
                $error = (object) ['code' => '400', 'description' => 'Password and confirm password must be the same'];
                $_SESSION['error'] = json_encode($error);

                return header('Location: /login');
            }

            $error = (object) ['code' => '400', 'description' => 'Username, password and email are required'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        $auth = new Auth($_ENV['AUTH_URL']);
        $auth = $auth->signup($body);
        if (isset($auth->error)) {
            $_SESSION['error'] = json_encode($auth->error);

            return header('Location: /login');
        }

        // $_SESSION['popup'] = json_encode((object) ["body" => "User created. Waiting for admin approval", "type" => "success"]);
        return header('Location: /login');
    }

    function signin(object $body)
    {
        if (empty($body->username) || empty($body->password)) {
            $error = (object) ['code' => '400', 'description' => 'Username and password are required'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        $auth = new Auth($_ENV['AUTH_URL']);
        $auth = $auth->signin($body);
        if (isset($auth->error)) {
            $_SESSION['error'] = json_encode($auth->error);

            return header('Location: /login');
        }

        if ($_ENV['ENVIRONMENT'] === 'production' && in_array($auth->role, ['parti', 'guest'])) {
            $error = (object) ['code' => '400', 'description' => "You don't have permission to access this site"];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /login');
        }

        if (isset($auth->center)) {
            setcookie('center', $auth->center, time() + (86400 * 30), '/');  // valid for 30 days
        }

        if (isset($auth->project)) {
            setcookie('project', json_encode($auth->project), time() + (86400 * 30), '/');  // valid for 30 days
        }

        setcookie('user_id', $auth->user_id, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('gAuth', $auth->gAuth, time() + (86400 * 30), '/');  // valid for 30 days
        setcookie('role', $auth->role, time() + (86400 * 30), '/');  // valid for 30 days

        // return header("Location: /admin");
        return header('Location: /');
    }

    function join(Auth $auth, object $body) {
        if (empty($body->project) || empty($body->pass)) {
            $error = (object) ['code' => '400', 'description' => 'There are missing fields'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin');
        }

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);
        $sql  = "SELECT name, center.name FROM ONLY $body->project LIMIT 1;";

        $result = $g_surreal->rawQuery($sql);
        if (isset($result->code) && $result->code != 200) {
            print_r($result);
            echo "error";
            return;
        }

        $center = $result[0]->result->center->name;
        $project = $result[0]->result->name;

        $auth = $auth->join($body->pass, $center, $project);
        if (isset($auth->error)) {
            $_SESSION['error'] = json_encode($auth->error);

            return header('Location: /admin');
        }

        setcookie('iAuth', $auth->iAuth, time() + (86400 * 30), '/');  // valid for 30 days

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
