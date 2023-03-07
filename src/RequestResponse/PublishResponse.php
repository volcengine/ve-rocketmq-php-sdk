<?php
namespace RMQ\RequestResponse;

use RMQ\Common\ArrayUtils;

class PublishResponse extends BaseResponse
{

  public $topic;
  public $queueId;
  public $queueOffset;
  public $msgId;

  public function __construct(array $json)
  {
    parent::__construct($json);

    $this->topic       = ArrayUtils::getArrayAttribute($json, ["topic"], "");
    $this->queueId     = ArrayUtils::getArrayAttribute($json, ["queueId"], "");
    $this->queueOffset = ArrayUtils::getArrayAttribute($json, ["queueOffset"], "");
    $this->msgId       = ArrayUtils::getArrayAttribute($json, ["msgId"], "");
  }

}

?>