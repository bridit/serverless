<?php

namespace Brid\Serverless\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class StderrLogger
{

  /**
   * @param array $config
   * @return LoggerInterface
   */
  public function __invoke(array $config): LoggerInterface
  {

    return new \Bref\Logger\StderrLogger($config['level'] ?? LogLevel::WARNING);

  }

}