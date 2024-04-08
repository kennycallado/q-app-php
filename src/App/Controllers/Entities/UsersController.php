<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class UsersController extends Render
{
    public function settings(Auth $auth)
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);

        $sql = '';
        $sql .= 'SELECT *, (SELECT id, name FROM projects WHERE center = $parent.id) AS projects FROM centers;';

        $multi = $g_surreal->rawQuery($sql);
        if (isset($multi->code)) {
            echo 'Error: ' . $multi->code;
            print_r($multi);

            return;
        }

        $num_rows = count($multi);
        $centers = $multi[--$num_rows]->result ?? [];

        $prepare = [
            'title' => 'Settings',
            'centers' => $centers,
            'error' => $error,
            'current' => [
                'center' => $auth->project->center ?? '',
                'project' => $auth->project->name ?? ''
            ]
        ];

        echo $this->view->render('pages/user/settings.html', $prepare);
        return;
    }
}
