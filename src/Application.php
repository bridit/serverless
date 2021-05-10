<?php

namespace Bridit\Serverless;

use Slim\App;
use DI\Container;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

class Application
{

  protected App $slim;

  public function __construct()
  {
    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
      mb_parse_str(urldecode($_SERVER['QUERY_STRING']), $_GET);
    }

    $dotenv = Dotenv::createImmutable(path());
    $dotenv->safeLoad();

    $container = new Container();

    $app = AppFactory::createFromContainer($container);

    $app->addErrorMiddleware((env('APP_ENV') === 'local'), true, true);

    $app->getRouteCollector()
      ->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponseArgs())
//  ->setCacheFile(__DIR__ . '/../bootstrap/cache/http-routes.cache')
    ;

    require path('/routes/http.php');

    $this->slim = clone $app;
  }

  public function run(): void
  {
    $this->slim->run();
  }

}