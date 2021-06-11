<?php

namespace Bridit\Serverless\Foundation;

use Bridit\Serverless\Foundation\Log\Logger;
use Carbon\Carbon;
use DI\Definition\ArrayDefinition;
use Dotenv\Dotenv;

class Application extends Container
{

  /**
   * @var string[]
   */
  protected array $config = ['app'];

  /**
   * @var array
   */
  protected array $serviceProviders = [];

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

    if (is_readable(path('.env'))) {
      $dotenv = Dotenv::createImmutable(path());
      $dotenv->safeLoad();
    }

    $storagePath = storage_path();

    if (! is_dir($storagePath)) {
      mkdir($storagePath, 0755, true);
    }

    return $this;

  }

  protected function bootConfig(): void
  {

    foreach (array_unique($this->config) as $config)
    {
      $fileName = path("/config/$config.php");

      if (is_readable($fileName)) {
        $this->set($config, new ArrayDefinition(require $fileName));
      }
    }

  }

  protected function bootLogger(): void
  {
    $this->set('logger', new Logger());
  }

  protected function bootProviders(): void
  {

    $this->serviceProviders = array_map(fn($item) => is_string($item) ? new $item : $item, array_merge($this->serviceProviders, config('app.providers')));

    $booted = [];

    foreach ($this->serviceProviders as $serviceProvider)
    {
      if (in_array($serviceProvider::class, $booted)) {
        continue;
      }

      $serviceProvider->register($this);
      $serviceProvider->boot();

      $booted[] = $serviceProvider::class;
    }

  }

  public function withConfig(array $config): static
  {
    $this->config = array_merge($this->config, $config);

    return $this;
  }

  public function withEloquent(): static
  {
    $this->serviceProviders[] = new \Bridit\Serverless\Foundation\Database\EloquentServiceProvider();

    return $this;
  }

  public function withSSMOAuthKeys(): static
  {
    $this->serviceProviders[] = new \Bridit\Serverless\Foundation\Auth\SSMOAuthServiceProvider();

    return $this;
  }

  public function withProvider($provider): static
  {
    $this->serviceProviders[] = $provider;

    return $this;
  }

  public function withProviders(array $providers): static
  {
    $this->serviceProviders = $providers;

    return $this;
  }

  public function start()
  {

    $this->bootConfig();
    $this->bootLogger();
    $this->bootProviders();

    $appConfig = config('app');

    date_default_timezone_set($appConfig['timezone'] ?? 'UTC');

    Carbon::setLocale($appConfig['locale'] ?? $appConfig['fallback_locale'] ?? 'en');

  }

}