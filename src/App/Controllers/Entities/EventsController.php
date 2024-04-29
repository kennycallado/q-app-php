<?php
namespace Src\App\Controllers\Entities;

use Src\Core\Render;
use Src\Utils\Auth;
use Src\Utils\Surreal;

class EventsController extends Render
{
    public function index(Auth $auth)
    {
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);
        $events = [];

        $sql = 'SELECT * FROM events;';

        $events = $i_surreal->rawQuery($sql);
        if (!is_array($events)) {
            echo 'Error: ' . $events->code;
            print_r($events);

            return;
        }

        $prepare = [
            'title' => 'Events',
            'events' => $events[0]->result
        ];

        echo $this->view->render('pages/events/index.html', $prepare);
        return;
    }

    public function show(Auth $auth, array $params)
    {
        $i_surreal = new Surreal($auth->project->center, $auth->project->name, $auth->pAuth);
        $event = [];

        $sql = "SELECT * FROM $params[id]";

        $event = $i_surreal->rawQuery($sql);
        if (!is_array($event)) {
            echo 'Error: ' . $event->code;
            print_r($event);

            return;
        }

        $prepare = [
            'title' => 'Event',
            'event' => $event[0]->result[0]
        ];

        echo $this->view->render('pages/events/details.html', $prepare);
        return;
    }
}
