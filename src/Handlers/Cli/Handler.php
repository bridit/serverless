<?php declare(strict_types=1);

namespace Bridit\Serverless\Handlers\Cli;

use Exception;
use Bref\Context\Context;

class Handler extends \Bridit\Serverless\Handlers\Handler implements \Bref\Event\Handler
{

  /**
   * @param mixed $event
   * @param \Bref\Context\Context|null $context
   * @return array|mixed|void
   * @throws \Exception
   */
  public function handle($event = null, Context $context = null)
  {
    parent::handle($event, $context);

    $event ??= [];

    if (!isset($event['command'])) {
      throw new Exception('Command not informed.');
    }

    $routes = require path('/routes/cli.php');

    if (!isset($routes[$event['command']])) {
      throw new Exception('Unknown command "' . $event['command'] . '".');
    }

    $args = $event;
    unset($args['command']);

    return (new $routes[$event['command']]($context))->handle($args);
  }

}
