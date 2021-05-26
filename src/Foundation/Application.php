<?php

namespace Bridit\Serverless\Foundation;

use Dotenv\Dotenv;
use DI\Definition\ArrayDefinition;
use Bridit\Serverless\Http\Request;
use Bridit\Serverless\Handlers\Handler;
use Bridit\Serverless\Foundation\Bootstrappers\Eloquent;

class Application extends Container
{

  protected $eloquentManager;
  
  /**
   * Application constructor.
   * @param string|null $basePath
   */
  protected function boot(string $basePath = null)
  {

    define('__BASE_PATH__', $basePath ?? realpath(__DIR__ . '/../../../../..'));

    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
      mb_parse_str(urldecode($_SERVER['QUERY_STRING']), $_GET);
    }

    $dotenv = Dotenv::createImmutable(path());
    $dotenv->safeLoad();

    return $this;

  }

  public function withConfig(array $config): static
  {
    $this->loadConfig($config);

    return $this;
  }

  public function withEloquent(): static
  {
    $this->bootEloquent();

    return $this;
  }

  protected function loadConfig(array $config)
  {

    foreach ($config as $fileName)
    {
      $this->set($fileName, new ArrayDefinition(require path("/config/$fileName.php")));
    }

  }

  protected function bootEloquent(): void
  {

    $this->eloquentManager = Eloquent::load($this);

    $this->set('db', fn() => $this->eloquentManager);

  }

}