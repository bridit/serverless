<?php

namespace Bridit\Serverless\Http\Middleware\Auth;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class VerifyWapiToken implements MiddlewareInterface
{

  /**
   * The URIs that should be excluded from verification.
   *
   * @var array
   */
  protected array $except = [
    //
  ];

  public function __construct(array $except = [])
  {
    if (count($except) > 0) {
      $this->except = $except;
    }
  }

  /**
   * @param Request $request
   * @param RequestHandler $handler
   * @return ResponseInterface
   * @throws Exception
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    if ($this->inExceptArray($request)) {
      return $handler->handle($request);
    }

    $this->verify($request);

    return $handler->handle($request);

  }

  /**
   * @param Request $request
   * @param RequestHandler $handler
   * @return ResponseInterface
   * @throws Exception
   */
  public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
  {
    return $this->process($request, $handler);
  }

  /**
   * Determine if the request has a URI that should pass through verification.
   *
   * @param Request $request
   * @return bool
   */
  protected function inExceptArray(ServerRequestInterface $request): bool
  {
    foreach ($this->except as $except) {
      if ($except === $request->getUri()->getPath()) {
        return true;
      }
    }

    return false;
  }

  /**
   * @param Request $request
   * @throws Exception
   */
  protected function verify(ServerRequestInterface $request): void
  {
    $wtoken = $request->hasHeader('Authorization')
      ? trim((string) preg_replace('/^(?:\s+)?Wapi\s/', '', $request->getHeaderLine('Authorization')))
      : null;

    if (blank($wtoken)) {
      throw new Exception('Missing "Authorization" header');
    }

    $token = base64_decode($wtoken);

    $this->verifyHash($token);
  }

  /**
   * @param string $token
   * @throws Exception
   */
  protected function verifyHash(string $token): void
  {
    if (!password_verify(env('WAPI_KEY'), $token)) {
      throw new Exception('Access token is invalid');
    }
  }

}
