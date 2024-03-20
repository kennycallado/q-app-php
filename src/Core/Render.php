<?php
namespace Src\Core;

use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFilter;

class Render
{
    public $view;

    function __construct()
    {
        $loader = new FilesystemLoader('../src/App/Views');
        $this->view = new Environment(
            $loader,
            $_ENV['ENVIRONMENT'] !== 'production' ? ['debug' => true] : []
        );

        if ($_ENV['ENVIRONMENT'] !== 'production') {
            $this->view->addExtension(new \Twig\Extension\DebugExtension);
        }

        $this->view->addExtension(new IntlExtension());
        $this->view->addGlobal('session', $_SESSION);
        $this->view->addGlobal('uri', $_SERVER['REQUEST_URI'] === '/' ? '/' : rtrim($_SERVER['REQUEST_URI'], '/'));

        /* Filtro en TWIG que permite convertir objeto en array */
        /* Esto me permite iterar por los objetos usando como key un 1 */
        /* y no el nombre completo de la propiedad del objeto */
        $this->view->addFilter(new TwigFilter('cast_to_array', function ($stdClassObject) {
            $response = [];

            /* Por cada propiedad del objeto devuelve una key con su value */
            foreach ($stdClassObject as $key => $value) {
                $response[] = array($key, $value);
            }

            return $response;
        }));
    }
}
