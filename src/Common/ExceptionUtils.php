<?php
namespace RMQ\Common;

use \RuntimeException;
use RMQ\Exception\MQRequestException;
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

  static function shouldTrowMQInvalidSecretKey($hasToken, RuntimeException $e)
  {

    if ($e instanceof MQRequestException) {
      return !$hasToken && $e->getCode() === 403;
    }

    return false;
  }
}

?>