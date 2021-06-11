<?php

namespace Bridit\Serverless\Foundation\Database;

use Bridit\Serverless\Foundation\Providers\ServiceProvider;

class EloquentServiceProvider extends ServiceProvider
{

  public function boot()
  {

    $databaseConfig = $this->container->get('database');
    
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

    $this->container->set('db', $capsule);

  }

}