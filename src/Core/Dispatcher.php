<?php
namespace Src\Core;

use Src\Core\Interfaces\IRequest;
use Src\Core\Interfaces\IRoutes;
use Src\Utils\Auth;

class Dispatcher
{
    private IRequest $currentRequest;
    private IRoutes $routeCollection;
    private array $routeList;
    private bool $is_protected;

    public function __construct(IRequest $request, IRoutes $routeCollection)
    {
        $this->is_protected = true;

        $this->currentRequest = $request;
        $this->routeCollection = $routeCollection;

        $this->routeList = $this->routeCollection->getRoutes();

        $this->dispatch();
    }

    private function action(string $method, string $route, ?array $params = null)
    {
        /* Si la ruta es protegida solo se puede acceder estando autorizado */
        if (
            !isset($this->routeList[$method][$route]['protected']) ||
            $this->routeList[$method][$route]['protected'] === true
        ) {
            $this->is_protected = true;

            if (
                isset($_COOKIE['gAuth']) ||
                isset($_COOKIE['user_id']) ||
                isset($_COOKIE['project']) ||
                isset($_COOKIE['pAuth']) ||
                isset($_COOKIE['role'])
            ) {
                $auth = new Auth($_ENV['AUTH_URL']);

                $auth->project = json_decode($_COOKIE['project']);
                $auth->user_id = $_COOKIE['user_id'];
                $auth->gAuth = $_COOKIE['gAuth'];
                $auth->pAuth = $_COOKIE['pAuth'];
                $auth->role = $_COOKIE['role'];
            } else {
                header('Location: /login');

                return;
            }
        } else {
            $this->is_protected = false;
        }

        $controllerClass = 'Src\\App\\Controllers\\' . $this->routeList[$method][$route]['controller'];
        $controller = new $controllerClass;
        $action = $this->routeList[$method][$route]['action'];

        /* Obtiene los parametros de la query string */
        $parsedUrl = parse_url($this->currentRequest->getUri());
        $queryString = $parsedUrl['query'] ?? '';
        $queryParams = $this->getQueryParams($queryString);

        $params = $params ?? [];  // Si no hay parametros los inicializa como array vacio
        $params = array_merge($params, $queryParams);

        /* Si llega petición POST obtiene el body */
        /* y lo pasa como primer parámetro despues resto */
        /* para peticiones PUT u otras deberia crear elfeif */
        $body = $method == 'POST' ? (object) $this->currentRequest->getPostBody() : null;

        if ($this->is_protected) {
            if ($body === null) {
                $controller->$action($auth, $params);
            } else {
                $controller->$action($auth, $body, $params);
            }
        } else {
            if ($body === null) {
                $controller->$action($params);
            } else {
                $controller->$action($body, $params);
            }
        }

        return;
    }

    private function str_replace_once($needle, $replace, $haystack)
    {
        if (($pos = strpos($haystack, $needle)) === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    private function dispatch()
    {
        $currentRoute = $this->currentRequest->getRoute();
        $method = $this->currentRequest->getMethod();
        $uri = $this->currentRequest->getUri();

        // Remove the query string from the current route
        $currentRoute = explode('?', $currentRoute)[0];

        if (isset($this->routeList['includes'][$currentRoute])) {
            $jsonFile = $this->routeList['includes'][$currentRoute];
            $uri = $this->str_replace_once($currentRoute, '', $uri);
            $uri = ($uri === '') ? '/' : $uri;
            $currentRoute = '/';

            $this->routeList = $this->routeCollection->getRoutesFromFile($jsonFile);
        }

        // Parse the URI and query string
        $parsedUrl = parse_url($uri);
        $parsedUrl['path'] = $parsedUrl['path'] ?? '/';  // ??
        $uri = urldecode($parsedUrl['path'] ?? '/');
        $queryString = urldecode($parsedUrl['query'] ?? '');

        // Parse the query parameters
        $queryParams = $this->getQueryParams($queryString);
        $match = $this->matchRoute($method, $uri, $currentRoute, $queryParams);

        if ($match) {
            $this->action($match['method'], $match['route'], $match['params'] ?? []);
        } else {
            header('Location: /');
        }

        return;
    }

    private function matchRoute(string $method, string $uri, string $currentRoute, array $queryParams)
    {
        $uriArray = explode('/', $uri);

        foreach ($this->routeList[$method] as $route => $object) {
            if ($uri === $route) {
                return ['method' => $method, 'route' => $route, 'params' => $queryParams ?? []];
            }

            if (!str_contains($route, $currentRoute)) {
                continue;
            }

            $routeArray = explode('/', $route);
            $routeArrayCount = count($routeArray);
            $uriArrayCount = count($uriArray);

            if ($uriArrayCount !== $routeArrayCount) {
                continue;
            }

            $params = [];
            $match = true;

            for ($i = 0; $i < $routeArrayCount; $i++) {
                if (str_contains($routeArray[$i], ':')) {
                    $paramName = substr($routeArray[$i], 1);
                    $params[$paramName] = $uriArray[$i];
                } else if ($routeArray[$i] !== $uriArray[$i]) {
                    $match = false;
                    break;
                }
            }

            // Merge the route parameters and query parameters
            $params = array_merge($params, $queryParams);

            if ($match) {
                return ['method' => $method, 'route' => $route, 'params' => $params];
            }
        }

        return null;
    }

    private function getQueryParams(string $queryString)
    {
        $queryParams = [];
        parse_str($queryString, $queryParams);

        return $queryParams;
    }
}
