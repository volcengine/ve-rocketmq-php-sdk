<?php
namespace RMQ\RequestResponse;

use RMQ\Common\ArrayUtils;
use RMQ\Model\MessageInfo;


class ConsumeResponse extends BaseResponse
{

  public $messages = [];

  public function __construct(array $rawJson)
  {
    parent::__construct($rawJson);

    $messageList = ArrayUtils::getArrayAttribute($rawJson, ["messages"], []);
    foreach ($messageList as $msg) {
      array_push($this->messages, new MessageInfo($msg));
    }
  }

}

?>