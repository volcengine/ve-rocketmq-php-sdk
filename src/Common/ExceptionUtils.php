<?php
namespace RMQ\Common;

use RMQ\Exception\MQRequestException;
use \RuntimeException;
use RMQ\Constants\RequestErrorCode;

class ExceptionUtils
{
  static function shouldTrowTokenTimeoutException($hasToken, RuntimeException $e)
  {

    if ($e instanceof MQRequestException) {
      $reqErrCode = $e->getRequestErrorCode();

      return $hasToken && $reqErrCode === RequestErrorCode::ClientNotFound;

    }

    return false;
  }
}

?>