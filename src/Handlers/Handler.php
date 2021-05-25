<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers;

use Exception;
use DI\Container;
use Dotenv\Dotenv;
use DI\ContainerBuilder;
use Bref\Context\Context;
use Bridit\Serverless\Bootstrappers\Eloquent;

class Handler
{

  protected ?Container $container = null;

  protected array $config = [];

  protected bool $eloquentORMEnabled = false;

  protected $eloquentManager;

  /**
   * Handler constructor.
   * @param string|null $basePath
   * @param array $config
   */
  public function __construct(string $basePath = null, array $config = [])
  {
    define('__BASE_PATH__', $basePath ?? realpath(__DIR__ . '/../../../../../..'));

    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
      mb_parse_str(urldecode($_SERVER['QUERY_STRING']), $_GET);
    }

    $dotenv = Dotenv::createImmutable(path());
    $dotenv->safeLoad();

    $this->config = $config;

  }

  /**
   * @param string|null $basePath
   * @param array $config
   * @return static
   */
  public static function create(string $basePath = null, array $config = []): static
  {
    return new static($basePath, $config);
  }

  public function withConfig(array $config): static
  {
    $this->config = $config;

    return $this;
  }

  public function withEloquent(): static
  {
    $this->eloquentORMEnabled = true;

    return $this;
  }

  public function boot(): static
  {
    $this->getContainer();

    return $this;
  }

  /**
   * @param string $name
   * @param $value
   * @return $this
   */
  public function set(string $name, $value): static
  {
    $this->container->set($name, $value);

    return $this;
  }

  protected function loadConfig(ContainerBuilder &$builder)
  {

    foreach ($this->config as $fileName)
    {
      $builder->addDefinitions([
        $fileName => require_once path("/config/$fileName.php"),
      ]);
    }

  }

  /**
   * @throws Exception
   */
  protected function getContainer(): Container
  {

    if (null !== $this->container) {
      return $this->container;
    }

    $builder = new ContainerBuilder();

    $this->loadConfig($builder);

    $container = $builder->build();

    if ($this->eloquentORMEnabled) {
      $this->bootEloquent($container);
    }

    $this->container = $container;

    return $this->container;

  }

  protected function bootEloquent(Container &$container): void
  {

    $this->eloquentManager = Eloquent::load($container);

    $container->set('db', fn($container) => $this->eloquentManager);

  }

  public function handle($event = null, Context $context = null)
  {

    if (null === $this->container) {
      $this->getContainer();
    }

  }

}
