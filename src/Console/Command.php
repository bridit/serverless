<?php

namespace Bridit\Serverless\Console;

use Bref\Context\Context;

class Command
{

  public function __construct(protected Context $context) {}

}