<?php
namespace RMQ\RequestResponse;

use RMQ\Common\ArrayUtils;

class OpenResponse extends BaseResponse
{
  public $clientToken;

  public function __construct(array $rawJson)
  {
    parent::__construct($rawJson);

    $this->clientToken = ArrayUtils::getArrayAttribute($rawJson, ["clientToken"]);
  }

}

?>