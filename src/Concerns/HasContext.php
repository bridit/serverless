<?php

namespace Brid\Serverless\Concerns;

use Bref\Context\Context;

trait HasContext
{

  /**
   * @param Context|null $context
   * @return Context|null
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