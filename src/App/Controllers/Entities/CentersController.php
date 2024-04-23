<?php
namespace Src\App\Controllers\Entities;

use Src\App\Models\Repositories\ProjectsRepository;
use Src\Core\Render;
use Src\Utils\Auth;
// use Src\Utils\Surreal;

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

    public function projects(Auth $auth)
    {
        $repo = new ProjectsRepository('global', 'main', $auth->gAuth);
        $projects = $repo->all('center');

        echo $this->view->render('pages/admin/center/projects/index.html', ['title' => 'Projects', 'projects' => $projects]);
        return;
    }

    public function projects_show(Auth $auth, $params)
    {
        $project = [
            'id' => $params['id']
        ];

        echo $this->view->render('pages/admin/center/projects/details.html', ['title' => 'Projects', 'project' => $project]);
        return;
    }
}
