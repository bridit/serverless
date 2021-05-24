<?php

if (! function_exists('path')) {
  /**
   * Get the path to the base of the install.
   *
   * @param  string  $path
   * @return string
   */
  function path(string $path = ''): string
  {
    return __BASE_PATH__ . \Illuminate\Support\Str::start($path, '/');
  }
}

if (! function_exists('config')) {
  /**
   * @param string $key
   * @param mixed|null $default
   * @return mixed
   */
  function config(string $key, mixed $default = null): mixed
  {
    $parts = explode('.', $key);

    $file = array_shift($parts);

    $config = require path("/config/$file.php");

    return \Illuminate\Support\Arr::get($config, implode('.', $parts), $default);
  }
}


if (! function_exists('request')) {
  /**
   * @param string|array|null $key
   * @param mixed|null $default
   * @return mixed
   */
  function request(string|array $key = null, mixed $default = null): mixed
  {
    $request = \Bridit\Serverless\Http\Request::fromGlobals();

    if (is_null($key)) {
      return $request;
    }

    if (is_array($key)) {
      return $request->only($key);
    }

    return $request->get($key, $default);
  }
}

if (! function_exists('response')) {
  /**
   * @param string $content
   * @param int $status
   * @param array $headers
   * @return \Bridit\Serverless\Http\Response
   */
  function response(string $content = '', int $status = 200, array $headers = []): \Bridit\Serverless\Http\Response
  {
    return new \Bridit\Serverless\Http\Response($status, $headers, $content);
  }
}

if (!function_exists('unaccent')) {

  function unaccent(string $value): string
  {
    return strtr($value, ['Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
      'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
      'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
      'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
      'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y']);
  }

}
