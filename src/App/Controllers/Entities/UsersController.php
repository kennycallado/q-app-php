<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
// use Src\Utils\Surreal;

class UsersController extends Render
{
    public function settings(Auth $auth)
    {
        echo $this->view->render('pages/user/settings.html', ['title' => 'Settings']);
        return;
    }
}
