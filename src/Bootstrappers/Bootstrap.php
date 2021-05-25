<?php

namespace Bridit\Serverless\Bootstrappers;

use DI\Container;

interface Bootstrap
{

  public static function load(Container $container);

}