<?php
namespace Src\App\Controllers;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class AdminController extends Render
{
    function dashboard(Auth $auth)
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);

        $sql = '';
        if (!isset($auth->pAuth)) {
            $sql .= 'SELECT *, (SELECT id, name FROM projects WHERE center = $parent.id) AS projects FROM centers;';
        }
        
        if (isset($auth->project)) {
            $sql .= "SELECT count() AS p_u_count FROM join WHERE out IS ". $auth->project->id ." AND in.role IS 'parti' GROUP BY p_u_count;";
            $sql .= "SELECT count() AS c_u_count FROM join WHERE out IN (SELECT VALUE id FROM projects WHERE center.name IS '" . $auth->project->center . "') AND in.role IS 'parti' GROUP BY c_u_count;";
            $sql .= "SELECT count() AS c_p_count FROM projects WHERE center.name = '" . $auth->project->center . "' GROUP BY c_p_count;";
        }

        $multi = $g_surreal->rawQuery($sql);
        if (isset($multi->code)) {
            echo 'Error: ' . $multi->code;
            print_r($multi);

            return;
        }

        $num_rows = count($multi);

        $c_p_count = $multi[--$num_rows]->result[0]->c_p_count ?? 0;
        $c_u_count = $multi[--$num_rows]->result[0]->c_u_count ?? 0;
        $p_u_count = $multi[--$num_rows]->result[0]->p_u_count ?? 0;
        $centers = $multi[--$num_rows]->result ?? [];

        $prepare = [
            'title' => 'Dashboard',
            'error' => $error,
            'joined' => isset($auth->pAuth) ? true : false,
            'centers' => $centers ?? [],
            'project' => [
                'users_count' => isset($p_u_count) ? $p_u_count : 0,
            ],
            'center' => [
                'projects_count' => isset($c_p_count) ? $c_p_count : 0,
                'users_count' => isset($c_u_count) ? $c_u_count : 0,
            ],
            'payments_count' => 1000,
            'current' => [
                'center' => $auth->project->center ?? '',
                'project' => $auth->project->name ?? ''
            ]
        ];

        echo $this->view->render('pages/admin/index.html', $prepare);
        return;
    }
}
