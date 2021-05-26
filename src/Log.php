<?php

namespace Bridit\Serverless;

use Psr\Log\AbstractLogger;
use Bref\Logger\StderrLogger;

/**
 * Class Log
 * @package Bridit\Serverless
 * @method static emergency($message, array $context = array())
 * @method static alert($message, array $context = array())
 * @method static critical($message, array $context = array())
 * @method static error($message, array $context = array())
 * @method static warning($message, array $context = array())
 * @method static notice($message, array $context = array())
 * @method static info($message, array $context = array())
 * @method static debug($message, array $context = array())
 */
class Log extends AbstractLogger
{

  protected StderrLogger $stderrLogger;

  public function __construct()
  {
    $this->stderrLogger = new StderrLogger();
  }

  public function log($level, $message, array $context = [])
  {
    $this->stderrLogger->{$level}($message, $context);
  }

  public static function __callStatic(string $name, array $arguments)
  {
    return call_user_func_array([get_called_class(), $name], $arguments);
  }

}