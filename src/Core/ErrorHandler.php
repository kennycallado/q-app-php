<?php
namespace Src\App\Core;

use Src\Core\Render;

class ErrorHandler extends Render
{
    /* recibe mensaje a mostrar */
    /* recibe ruta hacia donde redirecciona el botón */
    /* REDIRECCIÓN NECESITA: */
    /* ["redireccion" = true =>,"path" =>"","name" =>""] */

    function message($msg, $redirection = null)
    {
        if ($redirection) {
            echo $this->view->render(
                'error.html',
                ['numero' => 1, 'mensaje' => $msg, 'redireccion' => true, 'path' => $redirection['path'], 'name' => $redirection['name']]
            );
        } else {
            echo $this->view->render(
                'error.html',
                ['numero' => 1, 'mensaje' => $msg, 'redireccion' => false]
            );
        }
    }
}
