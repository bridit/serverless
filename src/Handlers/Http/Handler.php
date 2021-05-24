<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers\Http;

use Slim\App;
use DI\Container;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Bridit\Serverless\Http\Request;

class Handler
{

  /**
   * @var \Slim\App $slim
   */
  protected App $slim;

  /**
   * Handler constructor.
   * @param string|null $basePath
   */
  public function __construct(string $basePath = null)
  {

    define('__BASE_PATH__', $basePath ?? realpath(__DIR__ . '/../../../../../..'));

    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
      mb_parse_str(urldecode($_SERVER['QUERY_STRING']), $_GET);
    }

    $dotenv = Dotenv::createImmutable(path());
    $dotenv->safeLoad();

    $container = new Container();

    $app = AppFactory::createFromContainer($container);

    $app->addErrorMiddleware((env('APP_ENV') === 'local'), true, true);

    $app->addBodyParsingMiddleware();

    $app->getRouteCollector()
      ->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponseArgs())
//  ->setCacheFile(__DIR__ . '/../bootstrap/cache/http-routes.cache')
    ;

    require path('/routes/http.php');

    $this->slim = clone $app;
  }

  public function run(): void
  {
    $this->slim->run(Request::fromGlobals());
  }

}
