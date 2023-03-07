<?php
namespace RMQ\RequestResponse;

class CloseResponse extends BaseResponse
{
  public function __construct(array $json)
  {
    parent::__construct($json);
  }
}

?>