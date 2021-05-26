<?php

namespace Bridit\Serverless\Foundation;

use Bridit\Serverless\Contracts\Foundation\Container as ContainerContract;

class Container extends \DI\Container implements ContainerContract
{

  /**
   * The current globally available container (if any).
   *
   * @var ?ContainerContract
   */
  protected static ?ContainerContract $instance = null;

  /**
   * Get the globally available instance of the container.
   *
   * @param mixed ...$args
   * @return ContainerContract
   */
  public static function getInstance(...$args): ContainerContract
  {
    if (null !== static::$instance) {
      return static::$instance;
    }

    static::$instance = new static;

    if (method_exists(static::$instance, 'boot')) {
      static::$instance->boot(...$args);
    }

    return static::$instance;
  }

  /**
   * Set the shared instance of the container.
   *
   * @param  ContainerContract|null  $container
   * @return ContainerContract|null
   */
  public static function setInstance(ContainerContract $container = null): ?ContainerContract
  {
    return static::$instance = $container;
  }

}