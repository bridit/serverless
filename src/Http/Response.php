<?php

namespace Bridit\Serverless\Http;

class Response extends \Nyholm\Psr7\Response
{

  /**
   * @param array $data
   * @param int $status
   * @param array $headers
   * @param int $options
   * @return self
   */
  public function json($data = [], $status = 200, array $headers = [], $options = 0)
  {
    $this->getBody()->write(json_encode($data, $options));

    foreach ($headers as $key => $value)
    {
      $this->withHeader($key, $value);
    }
    
    return $this
      ->withHeader('Content-Type', 'application/json')
      ->withStatus($status);
  }

}