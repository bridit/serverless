<?php

namespace Bridit\Serverless;

use Psr\Log\AbstractLogger;
use Bref\Logger\StderrLogger;

class Log extends AbstractLogger
{

  protected StderrLogger $stderrLogger;

  public function __construct()
  {
    $this->stderrLogger = new StderrLogger();
  }

  public function log($level, $message, array $context = array())
  {
    $this->stderrLogger->{$level}($message, $context);
  }

}