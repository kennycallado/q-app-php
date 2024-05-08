<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class ResourcesController extends Render
{
    public function index(Auth $auth)
    {
        $resources = [];
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $sql = 'SELECT * FROM resources;';
        $resources = $i_surreal->rawQuery($sql);
        if (!is_array($resources)) {
            echo 'Error: ' . $resources->code;
            print_r($resources);

            return;
        }

        $prepare = [
            'title' => 'Resources',
            'error' => $error ?? null,
            'resources' => $resources[0]->result,
        ];

        echo $this->view->render('pages/resources/index.html', $prepare);
        return;
    }

    public function show(Auth $auth, array $params)
    {
        $resources = [];
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $sql = "SELECT * FROM $params[id];";
        $resources = $i_surreal->rawQuery($sql);
        if (!is_array($resources)) {
            echo 'Error: ' . $resources->code;
            print_r($resources);

            return;
        }

        $prepare = [
            'title' => 'Details',
            'error' => $error ?? null,
            'resource' => $resources[0]->result[0]
        ];

        echo $this->view->render('pages/resources/details.html', $prepare);
        return;
    
    }

    public function update(Auth $auth, object $body, array $params)
    {
        echo 'Unimplemented';
        return header('Location: /resources');
    }

    public function store(Auth $auth, object $body)
    {
        echo 'Unimplemented';
        return header('Location: /resources');
    }
}
