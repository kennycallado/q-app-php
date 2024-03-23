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

        $sql = "";
        if (isset($auth->project) && isset($auth->center)) {
            $sql .= "SELECT count() AS u_count FROM join WHERE out IN (SELECT VALUE id FROM projects WHERE center.name IS '".$auth->center."') AND in.role IS 'parti' GROUP BY u_count;";
            $sql .= "SELECT count() AS p_count FROM projects WHERE center.name = '".$auth->center."' GROUP BY p_count;";
            $sql .= "SELECT name, center.name FROM ONLY ". $auth->project->id ." LIMIT 1;";
        }

        $sql .= "SELECT id, name FROM projects;";

        $multi = $g_surreal->rawQuery($sql);
        if (isset($multi->code)) {
            echo "Error: ".$multi->code;
            print_r($multi);

            return;
        }

        $num_rows = count($multi);

        $projects = $multi[--$num_rows]->result ?? [];
        $current  = $multi[--$num_rows]->result ?? [];
        $p_count  = $multi[--$num_rows]->result[0]->p_count ?? 0;
        $u_count  = $multi[--$num_rows]->result[0]->u_count ?? 0;

        $prepare  = [
            'title' => 'Dashboard',
            'error' => $error,
            'joined' => isset($auth->iAuth) ? true : false,
            'projects_count' => $p_count,
            'users_count' => $u_count,
            'payments_count' => 1000,
            'projects' => $projects,
            'current' => [
                'center' => $current->center->name ?? '',
                'project' => $current->name ?? ''
            ]
        ];

        echo $this->view->render('pages/admin/index.html', $prepare);

        return;
    }
}
