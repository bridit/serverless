<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers;

use Bref\Context\Context;
use Bridit\Serverless\Foundation\Application;

class Handler extends Application
{

  protected ?Context $context;
  
  public function handle($event = null, Context $context = null)
  {
    $this->context = $this->getContext($context);

    $this->start();
  }

  /**
   * @param \Bref\Context\Context|null $context
   * @return \Bref\Context\Context|null
   */
  protected function getContext(Context $context = null): ?Context
  {
    if (!is_null($context)) {
      return $context;
    }

    $lambdaContext = isset($_SERVER['LAMBDA_INVOCATION_CONTEXT'])
      ? json_decode($_SERVER['LAMBDA_INVOCATION_CONTEXT'], true)
      : null;

    if (null === $lambdaContext) {
      return null;
    }

    return new Context($lambdaContext['awsRequestId'], $lambdaContext['deadlineMs'], $lambdaContext['invokedFunctionArn'], $lambdaContext['traceId']);
  }

}
