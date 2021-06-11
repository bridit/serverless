<?php

namespace Bridit\Serverless\Contracts\Foundation\Bootstrappers;

use DI\Container;

interface ServiceProvider
{

  public static function load(Container $container);

}