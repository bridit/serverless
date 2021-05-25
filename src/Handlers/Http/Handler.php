<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers\Http;

use Slim\App;
use Exception;
use Bref\Context\Context;
use Bridit\Serverless\Http\Request;
use DI\Bridge\Slim\Bridge as SlimBridge;

class Handler extends \Bridit\Serverless\Handlers\Handler
{

  protected ?App $slim = null;

  protected array $middleware = [];

  public function withMiddleware(array $middleware): static
  {
    $this->middleware = $middleware;

    return $this;
  }

  /**
   * @throws Exception
   */
  protected function bootSlim()
  {

    $app = SlimBridge::create($this->getContainer());

    $this->bootMiddleware($app);
    $this->bootRouter($app);

    $this->slim = clone $app;

  }

  protected function bootMiddleware(App &$app)
  {

    $app->addErrorMiddleware((env('APP_ENV') === 'local'), true, true);

    foreach ($this->middleware as $middleware)
    {
      $app->addMiddleware($middleware);
    }

  }

  protected function bootRouter(App &$app)
  {

    $app->addBodyParsingMiddleware();

    $app->getRouteCollector()
      ->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponseArgs())
//      ->setCacheFile(__DIR__ . '/../bootstrap/cache/http-routes.cache')
    ;

    require path('/routes/http.php');

  }

  public function handle($event = null, Context $context = null)
  {

    parent::handle($event, $context);

    if (null === $this->slim) {
      $this->bootSlim();
    }

    try {
      $this->slim->run(Request::fromGlobals());
    } catch (Exception $e) {
      throw $e;
    }

  }

}
