<?php
namespace RMQ\Exception;

use RMQ\Common\ArrayUtils;

class MQRequestException extends MQException
{
  /** @var int */
  private $httpCode;
  /** @var string */
  private $requestErrorCode;
  /** @var string */
  private $requestId;
  /** @var array */
  private $req = [];
  /** @var array */
  private $resp = [];

  public function __construct($httpMessage = "", $httpCode = 0, array $req = [], array $resp = [], $previous = null)
  {
    $message          = ArrayUtils::getArrayAttribute($resp, ["msg"], $httpMessage);
    $requestErrorCode = ArrayUtils::getArrayAttribute($resp, ["code"]);
    $code             = empty($requestErrorCode) ? $httpCode : $requestErrorCode;
    parent::__construct($message, $code, $previous);

    $this->httpCode         = $httpCode;
    $this->requestErrorCode = $requestErrorCode;
    $this->req              = $req;
    $this->resp             = $resp;
  }

  public function __toString()
  {
    $msg    = $this->message;
    $reqStr = json_encode($this->req);
    return "[RocketMQ-PHP-SDK]$msg; request body: $reqStr";
  }

  public function getRequestErrorCode()
  {
    return $this->requestErrorCode;
  }


  public function getHttpCode(){
    return $this->httpCode;
  }

}

?>