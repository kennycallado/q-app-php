<?php
namespace Src\App\Controllers\Entities;

use Src\App\Models\Repositories\ProjectsRepository;
use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class CentersController extends Render
{
    public function settings(Auth $auth)
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $prepare = [
            'title' => 'Settings',
            'error' => $error
        ];

        echo $this->view->render('pages/admin/center/settings.html', $prepare);
        return;
    }

    public function projects_index(Auth $auth)
    {
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        unset($_SESSION['error']);

        $repo = new ProjectsRepository('global', 'main', $auth->gAuth);
        $projects = $repo->where("center.name is '" . $auth->project->center . "' and id is not '" . $auth->project->id . "'");

        $prepare = [
            'title' => 'Projects',
            'projects' => $projects,
            'error' => $error
        ];

        echo $this->view->render('pages/admin/center/projects/index.html', $prepare);
        return;
    }

    public function projects_show(Auth $auth, $params)
    {
        $project = [];

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);
        $repo = new ProjectsRepository('global', 'main', $auth->gAuth);

        $project = $repo->findBy('id', $params['id'], 'center.name');

        $sql = '';
        $sql .= 'SELECT count() AS p_u_count FROM join WHERE out IS ' . $params['id'] . " AND in.role IS 'parti' GROUP BY p_u_count;";

        $multi = $g_surreal->rawQuery($sql);
        if (isset($multi->code)) {
            echo 'Error: ' . $multi->code;
            print_r($multi);

            return;
        }

        $num_rows = count($multi);
        $project = $project[0];
        $project->users_count = $multi[--$num_rows]->result[0]->p_u_count ?? 0;

        $prepare = [
            'title' => 'Details',
            'project' => $project ?? []
        ];

        echo $this->view->render('pages/admin/center/projects/details.html', $prepare);
        return;
    }

    public function projects_store(Auth $auth, $params)
    {
        if (in_array($auth->role, ['thera', 'parti', 'guest'])) {
            $error = (object) ['code' => '400', 'details' => 'You are not authorized to create projects.'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin/center/projects');
        }

        if (!isset($params->name) || !isset($params->center_name)) {
            $error = (object) ['code' => '400', 'details' => 'Name and Center Name are required.'];
            $_SESSION['error'] = json_encode($error);

            return header('Location: /admin/center/projects');
        }

        // if (strlen($params->name) < 3 || strlen($params->center_name) < 3) {
        //     $error = (object) ['code' => '400', 'details' => 'Name and Center Name must be at least 3 characters long.'];
        //     $_SESSION['error'] = json_encode($error);

        //     return header('Location: /admin/center/projects');
        // }

        $g_surreal = new Surreal('global', 'main', $auth->gAuth);
        $repo = new ProjectsRepository('global', 'main', $auth->gAuth);

        $project = $repo->create($params->name, $params->center_name, $params->keys ?? null);
        if (!is_array($project)) {
            echo 'Error: ' . $project;
            print_r($project);

            return;
        } else {
            $project = $project[0];
        }

        sleep(1);

        // assign current user to the project
        $sql = '';
        $sql .= "UPDATE $auth->user_id SET project = $project->id;";
        $sql .= "UPDATE $auth->user_id SET project = " . $auth->project->id . ';';

        $res = $g_surreal->rawQuery($sql);
        if (isset($res->code)) {
            echo 'Error: ' . $res->code;
            print_r($res);

            return;
        }

        // $num_rows = count($multi);

        return header("Location: /admin/center/projects/$project->id");
    }

    // public function projects_edit(Auth $auth, $params)
    // {
    //     $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
    //     unset($_SESSION['error']);

    //     // no longer needed
    //     // if ($params['id'] === 'create') {
    //     //     $prepare = [
    //     //         'title' => 'Create',
    //     //         'error' => $error
    //     //     ];

    //     //     echo $this->view->render('pages/admin/center/projects/create.html', $prepare);
    //     //     return;
    //     // }

    //     $project = [];

    //     $g_surreal = new Surreal('global', 'main', $auth->gAuth);
    //     $repo = new ProjectsRepository('global', 'main', $auth->gAuth);

    //     $project = $repo->findBy('id', $params['id'], 'center.name');

    //     $sql = '';
    //     $sql .= 'SELECT count() AS p_u_count FROM join WHERE out IS ' . $params['id'] . " AND in.role IS 'parti' GROUP BY p_u_count;";

    //     $multi = $g_surreal->rawQuery($sql);
    //     if (isset($multi->code)) {
    //         echo 'Error: ' . $multi->code;
    //         print_r($multi);

    //         return;
    //     }

    //     $num_rows = count($multi);
    //     $project = $project[0];
    //     $project->users_count = $multi[--$num_rows]->result[0]->p_u_count ?? 0;

    //     $prepare = [
    //         'title' => 'Details',
    //         'project' => $project ?? [],
    //         'error' => $error
    //     ];

    //     echo $this->view->render('pages/admin/center/projects/details.html', $prepare);
    //     return;
    // }
}
