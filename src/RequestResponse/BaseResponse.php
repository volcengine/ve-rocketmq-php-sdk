<?php
namespace RMQ\RequestResponse;

abstract class BaseResponse
{
  private $rawJson;

  public function __construct(array $json)
  {
    $this->rawJson = $json;
  }

  public function getRawJson()
  {
    return $this->rawJson;
  }
}

?>