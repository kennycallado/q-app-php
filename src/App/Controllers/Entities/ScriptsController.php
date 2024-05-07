<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class ScriptsController extends Render
{
    public function index(Auth $auth)
    {
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);
        $scripts = [];

        $sql = 'SELECT * FROM scripts;';
        $scripts = $i_surreal->rawQuery($sql);
        if (!is_array($scripts)) {
            echo 'Error: ' . $scripts->code;
            print_r($scripts);

            return;
        }

        $prepare = [
            'title' => 'Scripts',
            'error' => $error ?? null,
            'scripts' => $scripts[0]->result,
        ];

        echo $this->view->render('pages/scripts/index.html', $prepare);
        return;
    }

    public function show(Auth $auth, array $params)
    {
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);
        $script = [];

        $sql = "SELECT * FROM $params[id];";
        $script = $i_surreal->rawQuery($sql);
        if (!is_array($script)) {
            echo 'Error: ' . $script->code;
            print_r($script);

            return;
        }

        $prepare = [
            'title' => 'Details',
            'editor' => true, // active some resources in the view (css, js)
            'error' => $error ?? null,
            'script' => $script[0]->result[0]
        ];

        echo $this->view->render('pages/scripts/details.html', $prepare);
        return;
    }

    public function update(Auth $auth, object $body, array $params)
    {
        if (!isset($params['id']) || !isset($body->name) || !isset($body->code)) {
            echo 'Error: Missing required fields';
            return;
        }

        $script = [];

        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $sql = "UPDATE $params[id] SET name = '$body->name', code = '$body->code' WHERE id = $params[id];";
        $script = $i_surreal->rawQuery($sql);
        if (!is_array($script)) {
            echo 'Error: ' . $script->code;
            print_r($script);

            return;
        }

        return header('Location: /scripts/' . urlencode($params['id']));
    }

    public function store(Auth $auth, object $body)
    {
        echo 'Unimplemented';
        return header('Location: /scripts');
    }
}
