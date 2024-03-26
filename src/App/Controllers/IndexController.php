<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;

class IndexController extends Render
{
    function index(Auth $auth, array $params)
    {
        $prepare = [
            'title' => 'Home',
            'auth' => $auth,
            'params' => $params
        ];

        echo $this->view->render('pages/index/index.html', $prepare);
        return;
    }
}
