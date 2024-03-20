<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;

class AuthController extends Render
{
  function login() {
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
    unset($_SESSION['error']);

    echo $this->view->render("pages/auth/login.html", ["title" => "Login", "error" => $error]);
    return;
  }

  function signin(object $body) { // could fails
    if (empty($body->username) || empty($body->password)) {
      $error = (object) ["code" => "400", "description" => "Username and password are required"];
      $_SESSION['error'] = json_encode($error);

      return header("Location: /login");
    }

    $auth = new Auth($_ENV['AUTH_URL']); // Don't like use $_ENV here
    $auth = $auth->signin($body);
    if (isset($auth->error)) {
      $_SESSION['error'] = json_encode($auth->error);

      return header("Location: /login");
    }

    if ($_ENV['ENVIRONMENT'] === "production" &&  in_array($auth->role, ['parti', 'guest'])) {
      $error = (object) ["code" => "400", "description" => "You don't have permission to access this site"];
      $_SESSION['error'] = json_encode($error);

      return header("Location: /login");
    }

    if (isset($auth->project)) {
      setcookie('project', json_encode($auth->project), time() + (86400 * 30), "/"); // valid for 30 days
    }

    setcookie('user_id', $auth->user_id, time() + (86400 * 30), "/"); // valid for 30 days
    setcookie('gAuth', $auth->gAuth, time() + (86400 * 30), "/"); // valid for 30 days
    setcookie('role', $auth->role, time() + (86400 * 30), "/"); // valid for 30 days

    // return header("Location: /admin");
    return header("Location: /");
  }

  function logout() {
    // remove session, cookies, etc

    setcookie("gAuth", "", -1 , "/");
    setcookie("iAuth", "", -1 , "/");
    setcookie("role", "", -1 , "/");
    setcookie("user_id", "", -1 , "/");
    setcookie("project", "", -1 , "/");

    session_unset();

    return header("Location: /");
  }
}
