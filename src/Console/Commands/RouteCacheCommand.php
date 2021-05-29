<?php

namespace Bridit\Serverless\Console\Commands;

use Slim\App;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased;

class RouteCacheCommand
{

  /**
   * @return void
   */
  public function handle(): void
  {

    $cacheFile = path('/bootstrap/cache/http-routes.cache');

    if (file_exists($cacheFile)) {
      unlink($cacheFile);
    }

    /**
     * @var App $app
     */
    $app = require path('/bootstrap/slim.php');

    /**
     * @var \Slim\Routing\Route[] $routes
     */
    $routes = $app
      ->getRouteCollector()
      ->getRoutes();

    $routeCollector = new RouteCollector(
      new Std(),
      new GroupCountBased()
    );

    foreach ($routes as $route)
    {
      foreach ($route->getMethods() as $method)
      {
        $pattern = $route->getPattern();
        $handler = $route->getIdentifier();

        $routeCollector->addRoute($method, $pattern, $handler);
      }
    }

    file_put_contents($cacheFile, '<?php return '  . var_export($routeCollector->getData(), true) . ';');

  }

}