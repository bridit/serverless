<?php

namespace Bridit\Serverless\Bootstrappers;

use DI\Container;

class Eloquent implements Bootstrap
{

  public static function load(Container $container)
  {

    $databaseConfig = $container->get('database');
    $default = $databaseConfig['default'];
    $connections = $databaseConfig['connections'] ?? [];
    $manager = '\Illuminate\Database\Capsule\Manager';

    $capsule = new $manager;

    foreach ($connections as $name => $config)
    {
      $capsule->addConnection($config, ($name === $default ? 'default' : $name));
    }

//    $capsule->setEventDispatcher(new Dispatcher(new Container));
    
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
    
  }

}