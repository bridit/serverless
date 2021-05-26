<?php

namespace Bridit\Serverless\Contracts\Foundation;

use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;
use DI\Definition\Source\MutableDefinitionSource;

interface Container
{

  public function __construct(
    MutableDefinitionSource $definitionSource = null,
    ProxyFactory $proxyFactory = null,
    ContainerInterface $wrapperContainer = null
  );

}