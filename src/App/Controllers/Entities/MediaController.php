<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class MediaController extends Render
{
    public function index(Auth $auth)
    {
        $media = [];
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $sql = 'SELECT * FROM media;';
        $media = $i_surreal->rawQuery($sql);
        if (!is_array($media)) {
            echo 'Error: ' . $media->code;
            print_r($media);

            return;
        }

        $prepare = [
            'title' => 'Media',
            'error' => $error ?? null,
            'media' => $media[0]->result,
        ];

        echo $this->view->render('pages/media/index.html', $prepare);
        return;
    }

    public function show(Auth $auth, array $params)
    {
        $media = [];
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);

        $sql = "SELECT * FROM $params[id];";
        $media = $i_surreal->rawQuery($sql);
        if (!is_array($media)) {
            echo 'Error: ' . $media->code;
            print_r($media);

            return;
        }

        $prepare = [
            'title' => 'Details',
            'error' => $error ?? null,
            'media' => $media[0]->result[0]
        ];

        echo $this->view->render('pages/media/details.html', $prepare);
        return;
    }

    public function update(Auth $auth, object $body, array $params)
    {
        echo 'Unimplemented';
        return header('Location: /media');
    }

    public function store()
    {
        echo 'Unimplemented';
        return header('Location: /media');
    }
}
