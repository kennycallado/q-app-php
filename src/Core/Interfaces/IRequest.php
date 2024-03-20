<?php
namespace Src\Core\Interfaces;

interface IRequest
{
  public function getRoute();
  public function getMethod();
  public function getPostBody();
  public function getUri();
}
