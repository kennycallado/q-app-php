<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

use Src\App\Models\Repositories\UserRepository;

class AdminController extends Render
{
  function dashboard(Auth $auth) {
    $surreal = new Surreal("global", "main", $auth->gAuth);
    $multi = $surreal->rawQuery("SELECT count() as projects_count FROM ONLY projects GROUP BY projects_count LIMIT 1; RETURN 'foo';");

    $users_repo = new UserRepository("global", "main", $auth->gAuth);
    $users = $users_repo->all();

    $projects_count = isset($multi[0]->result->projects_count) ? $multi[0]->result->projects_count : 0;
    $payments_count = 1000;

    $prepare = [
      "title" => "Dashboard",
      "projects_count" => $projects_count,
      "users_count" => count($users[0]->result),
      "payments_count" => $payments_count
    ];

    echo $this->view->render("pages/admin/dashboard.html", $prepare);

    return;
  }

}
