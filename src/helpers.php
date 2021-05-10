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
