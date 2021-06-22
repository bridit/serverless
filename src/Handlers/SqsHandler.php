<?php

namespace Brid\Serverless\Handlers;

use Bref\Context\Context;
use Bref\Event\InvalidLambdaEvent;
use Bref\Event\Sqs\SqsEvent;
use Brid\Serverless\Concerns\HasContext;
use Throwable;

class SqsHandler extends \Brid\Sqs\SqsHandler implements \Bref\Event\Handler
{

  use HasContext;

  /**
   * @inheritDoc
   */
  protected function boot(string $basePath = null): static
  {
    define('APP_HANDLER_TYPE', 'cli');

    return parent::boot($basePath);
  }

  /**
   * @param mixed $event
   * @param Context|null $context
   * @return void
   * @throws InvalidLambdaEvent
   * @throws Throwable
   */
  public function handle($event = null, $context = null)
  {
    $this->context = $this->getContext($context);

    $this->handleSqs(new SqsEvent($event), $context);
  }

}