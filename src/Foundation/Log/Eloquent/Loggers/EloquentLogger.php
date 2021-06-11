<?php

declare(strict_types=1);

namespace Bridit\Serverless\Foundation\Log\Eloquent\Loggers;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Bridit\Serverless\Foundation\Log\Eloquent\Handler\EloquentHandler;
use Bridit\Serverless\Foundation\Log\Eloquent\Processor\ContextProcessor;
use Bridit\Serverless\Foundation\Log\Eloquent\Processor\RequestProcessor;

class EloquentLogger
{

  public function __invoke(array $config): LoggerInterface
  {

    $logger = new Logger('eloquent');
    $logger->pushHandler(new EloquentHandler());
    $logger->pushProcessor(new ContextProcessor());
    $logger->pushProcessor(new RequestProcessor());

    return $logger;

  }

}
