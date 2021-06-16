<?php declare(strict_types=1);

namespace Brid\Serverless\Handlers;

use Brid\Serverless\Concerns\HasContext;

class Handler extends \Brid\Core\Handlers\Handler implements \Bref\Event\Handler
{

  use HasContext;

  /**
   * @inheritDoc
   */
  public function handle($event = null, $context = null)
  {
    $this->context = $this->getContext($context);
  }

}
