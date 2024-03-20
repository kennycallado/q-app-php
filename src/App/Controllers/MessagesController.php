<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;

class MessagesController extends Render
{
  function index(Auth $auth) {
    $prepare = [
      "title" => "Messages",
      "auth" => $auth
    ];

    echo $this->view->render("pages/messages/index.html", $prepare);

    return;
  }
}
