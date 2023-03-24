<?php
namespace RMQ\Common;

class NumberUtils
{
  /**
   * @param mixed $val
   * @param number $min
   * @param number $max
   * @return  bool 
   */
  static function intCheck($val, $min, $max)
  {
    if (!is_int($val)) {
      return false;
    }

    if ($val > $max || $val < $min) {
      return false;
    }

    return true;
  }
}


?>