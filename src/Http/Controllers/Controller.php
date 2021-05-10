<?php

namespace Bridit\Serverless\Http\Controllers;

use Psr\Container\ContainerInterface;

class Controller
{

  public function __construct(protected ContainerInterface $container) {}

}