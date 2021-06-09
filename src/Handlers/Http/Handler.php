<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers\Http;

use Slim\App;
use Exception;
use Bref\Context\Context;
use Bridit\Serverless\Http\Request;
use DI\Bridge\Slim\Bridge as SlimBridge;
use Bridit\Serverless\Handlers\Exceptions\ShutdownHandler;
use Bridit\Serverless\Handlers\Exceptions\HttpErrorHandler;
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
  protected function getSlimInstance(): App
  {

    $app = SlimBridge::create(app());

    $this->bootRequest();
    $this->bootMiddleware($app);
    $this->bootRouter($app);

    return $app;

  }

  protected function bootMiddleware(App &$app): void
  {

    $this->bootErrorHandler($app);

    foreach ($this->middleware as $middleware)
    {
      $app->addMiddleware($middleware);
    }

  }

  protected function bootErrorHandler(App &$app): void
  {

    $displayErrorDetails = env('APP_ENV') === 'local';
    $callableResolver = $app->getCallableResolver();
    $responseFactory = $app->getResponseFactory();

    $request = $this->get('request');

    $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
    $shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
    register_shutdown_function($shutdownHandler);

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add Error Handling Middleware
    $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);
    $errorMiddleware->setDefaultErrorHandler($errorHandler);

  }

  protected function bootRouter(App &$app): void
  {

    $app->getRouteCollector()
      ->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestResponseArgs())
//      ->setCacheFile(__DIR__ . '/../bootstrap/cache/http-routes.cache')
    ;

    require path('/routes/http.php');

  }

  protected function bootRequest(): void
  {
    $request = (new BodyParsingMiddleware())->execute(Request::fromGlobals());

    $this->set('request', fn() => $request);
  }

  public function handle($event = null, Context $context = null)
  {
    $this->bootProviders();

    $context = $this->getContext($context);

    parent::handle($event, $context);

    $app = $this
      ->getSlimInstance();

    $app
      ->run($this->get('request'));

  }

}
