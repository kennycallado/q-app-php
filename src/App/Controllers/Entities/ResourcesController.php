<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class ResourcesController extends Render
{
    public function index(Auth $auth, array $params)
    {
        // error handling

        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 10;
        $start = ($page - 1) * $limit;

        $resources = [];
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $sql = "
            SELECT count() FROM ONLY resources GROUP BY count LIMIT 1;
            SELECT * FROM resources LIMIT $limit START $start;";
        $resources = $i_surreal->rawQuery($sql);
        if (!is_array($resources)) {
            echo 'Error: ' . $resources->code;
            print_r($resources);

            return;
        }

        $total = $resources[0]->result->count;
        $resources = $resources[1]->result;

        $prepare = [
            'title' => 'Resources',
            'error' => $error ?? null,
            'pagination' => [
                'base' => '/resources',
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ],
            'resources' => $resources
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
