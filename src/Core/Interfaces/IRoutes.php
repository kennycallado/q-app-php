<?php
namespace Src\Core\Interfaces;

interface IRoutes
{
  public function getRoutes();
  public function getRoutesFromFile($file);
}

