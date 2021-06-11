<?php

declare(strict_types=1);

namespace Bridit\Serverless\Foundation\Log\Eloquent\Processor;

use Bridit\Serverless\Http\Request;

class RequestProcessor
{

  public function __invoke(array $record): array
  {

    /**
     * @var Request $request
     */
    $request = request();

    $record['app_id'] = $request->header('app-id', '3785e185-3bd7-455d-8b55-1a171534ddd3');
    $record['client_id'] = $request->getAttribute('oauth_client_id');
    $record['customer_id'] = null;
    $record['user_id'] = $request->getAttribute('oauth_user_id');

    $url = $request->getUri()->getScheme() . '://' .
      $request->getUri()->getHost() .
      ($request->getUri()->getPort() ?? '') .
      $request->getUri()->getPath() .
      $request->getUri()->getQuery();

    $record['extra']['server'] = $request->server('SERVER_ADDR');
    $record['extra']['host'] = $request->getUri()->getHost();
    $record['extra']['origin'] = $request->header('origin');
    $record['extra']['uri'] = $request->getUri()->getPath();
    $record['extra']['request'] = [
      'client_ip' => $request->getClientIp(),
      'http_method' => $request->getMethod(),
      'headers' => $request->getHeaders(),
      'params' => $request->all(),
      'url' => $url,
      'user_agent' => $request->getClientUserAgent(),
    ];

    return $record;
  }

}
