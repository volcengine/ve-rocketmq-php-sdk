<?php
namespace RMQ\Common;

class ArrayUtils
{
  static function getArrayAttribute(array $arr, array $paths, $default = null)
  {
    $p = $arr;
    foreach ($paths as $key) {
      if (!array_key_exists($key, $p)) {
        return $default;
      }
      $p = $arr[$key];
    }

    return $p;
  }
}

?>