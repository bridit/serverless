<?php

namespace Bridit\Serverless\Foundation\Auth;

use Bridit\Serverless\Foundation\Providers\ServiceProvider;
use Exception;
use AsyncAws\Ssm\SsmClient;
use AsyncAws\Ssm\Input\GetParametersRequest;
use Illuminate\Support\Arr;

class SSMOAuthServiceProvider extends ServiceProvider
{

  protected SsmClient $ssm;
  protected array $config;

  public function __construct()
  {
    $this->config = [
      'aws' => $this->container->get('aws') ?? [],
      'auth' => $this->container->get('auth') ?? [],
    ];

    $this->ssm = new SsmClient($this->getAwsConfig());
  }

  private function getAwsConfig(): array
  {
    return [
      'accessKeyId' => $this->config['aws']['credentials']['key'],
      'accessKeySecret' => $this->config['aws']['credentials']['secret'],
      'region' => $this->config['aws']['region'],
    ];
  }

  public function boot()
  {
    $privateKeyPath = storage_path('/oauth-private.key');
    $publicKeyPath = storage_path('/oauth-public.key');

    if (is_readable($privateKeyPath) && is_readable($publicKeyPath)) {
      return;
    }

    $keys = $this->getKeys();

    if (blank($keys['private']) || blank($keys['public'])) {
      throw new Exception('Passport keys not set on AWS SSM.');
    }

    $this->saveKeys($keys['private'], $keys['public']);

  }

  /**
   * @return array
   */
  private function getKeys(): array
  {

    $keys = [
      'private' => Arr::get($this->config, 'auth.jwt.ssm.private'),
      'public' => Arr::get($this->config, 'auth.jwt.ssm.public'),
    ];

    $parameters = $this->ssm->getParameters(new GetParametersRequest([
      'Names' => array_values($keys),
      'WithDecryption' => true,
    ]))->getParameters();

    foreach ($parameters as $parameter)
    {
      if ($keys['private'] === $parameter->getName()) {
        $keys['private'] = $parameter->getValue();
        continue;
      }

      $keys['public'] = $parameter->getValue();
    }

    return $keys;

  }

  /**
   * @param string $privateKey
   * @param string $publicKey
   */
  private function saveKeys(string $privateKey, string $publicKey): void
  {
    $privateKeyPath = storage_path('/oauth-private.key');
    $publicKeyPath = storage_path('/oauth-public.key');

    file_put_contents($privateKeyPath, $privateKey);
    file_put_contents($publicKeyPath, $publicKey);
    chmod($privateKeyPath, 0660);
    chmod($publicKeyPath, 0660);
  }

}