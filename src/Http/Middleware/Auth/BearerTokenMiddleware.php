<?php

namespace Bridit\Serverless\Http\Middleware\Auth;

use Bridit\Serverless\Handlers\Exceptions\OAuthServerException;
use Bridit\Serverless\Foundation\Auth\AccessTokenRepository;
use Bridit\Serverless\Foundation\Auth\Validators\BearerTokenValidator;
use Bridit\Serverless\Http\Request;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;

class BearerTokenMiddleware implements MiddlewareInterface
{

  /**
   * @var array
   */
  protected array $scopes;

  public function __construct(string|array $scope)
  {
    $this->scopes = is_array($scope)
      ? $scope
      : array_map('trim', explode(',', $scope));
  }

  /**
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $handler
   * @return ResponseInterface
   * @throws OAuthServerException
   * @todo Implement scope validation
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {

    try {
      $bearerTokenValidator = new BearerTokenValidator(new AccessTokenRepository());

      $request = $bearerTokenValidator
        ->setPublicKey('oauth-public.key')
        ->validateAuthorization($request);

      app()->set('request', $request);

    } catch (Exception $e) {
      throw $e;
    }

    return $handler->handle($request);

  }

  /**
   * @param Request $request
   * @param RequestHandler $handler
   * @return ResponseInterface
   * @throws OAuthServerException
   */
  public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
  {
    return $this->process($request, $handler);
  }
  
}