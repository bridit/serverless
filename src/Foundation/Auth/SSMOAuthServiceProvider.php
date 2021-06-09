<?php

namespace Bridit\Serverless\Foundation\Auth;

use AsyncAws\Ssm\SsmClient;
use AsyncAws\Ssm\Input\GetParametersRequest;

class SSMOAuthServiceProvider
{

  protected SsmClient $ssm;
  
  public function __construct()
  {
    $this->ssm = new SsmClient($this->getConfig());
  }

  private function getConfig(): array
  {
    $config = config('aws');

    return [
      'accessKeyId' => $config['credentials']['key'],
      'accessKeySecret' => $config['credentials']['secret'],
      'region' => $config['region'],
    ];
  }
  
  public function boot()
  {
    $privateKeyPath = path('/storage/oauth-private.key');
    $publicKeyPath = path('/storage/oauth-public.key');

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
      'private' => config('auth.jwt.ssm.private'),
      'public' => config('auth.jwt.ssm.public'),
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
    $privateKeyPath = path('/storage/oauth-private.key');
    $publicKeyPath = path('/storage/oauth-public.key');

    file_put_contents($privateKeyPath, $privateKey);
    file_put_contents($publicKeyPath, $publicKey);
    chmod($privateKeyPath, 0660);
    chmod($publicKeyPath, 0660);
  }

}