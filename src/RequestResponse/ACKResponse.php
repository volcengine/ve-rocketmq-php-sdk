<?php
namespace RMQ\RequestResponse;

use RMQ\Common\ArrayUtils;

class ACKResponse extends BaseResponse
{
  public $failedHandles = [];

  public function __construct(array $rawJson)
  {
    parent::__construct($rawJson);
    $this->failedHandles = ArrayUtils::getArrayAttribute($rawJson, ["failHandles"], []);

  }

}

?>