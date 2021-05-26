<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers\Http;

use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Exception;
use Bref\Context\Context;
use Bridit\Serverless\Http\Request;
use DI\Bridge\Slim\Bridge as SlimBridge;
use Bridit\Serverless\Http\Middleware\BodyParsingMiddleware;

class Handler extends \Bridit\Serverless\Handlers\Handler
{

  protected array $middleware = [];

  public function withMiddleware(array $middleware): static
  {
    $this->middleware = $middleware;

    return $this;
  }

  /**
   * @throws Exception
   */
  protected function getSlimInstance()
  {

    $app = SlimBridge::create(app());

    $this->bootMiddleware($app);
    $this->bootRouter($app);
    $this->bootRequest();

    return $app;

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

    $app->getRouteCollector()
      ->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponseArgs())
//      ->setCacheFile(__DIR__ . '/../bootstrap/cache/http-routes.cache')
    ;

    require path('/routes/http.php');

  }

  protected function bootRequest()
  {
    $request = (new BodyParsingMiddleware())->execute(Request::fromGlobals());

    $this->set('request', fn() => $request);
  }

  public function handle($event = null, Context $context = null)
  {

    $context = $this->getContext($context);

    parent::handle($event, $context);

    $this
      ->getSlimInstance()
      ->run($this->get('request'));

  }

}
