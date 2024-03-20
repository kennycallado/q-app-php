<?php
namespace Src\Core;

use Src\Core\Interfaces\IRoutes;

class RouteCollection implements IRoutes
{
  private $path = __DIR__ . '/../App/Router/';
  private $routes;

  function __construct() {
    $this->routes = json_decode(file_get_contents($this->path . 'index.json'), true);
  }

  /**
   * Get the value of routes
   */
  public function getRoutes() {
    return $this->routes;
  }

  public function getRoutesFromFile($file) {
    return json_decode(file_get_contents($this->path . $file), true);
  }
}
