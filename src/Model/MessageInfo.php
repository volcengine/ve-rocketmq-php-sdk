<?php
namespace RMQ\Model;

use RMQ\Common\ArrayUtils;

class MessageInfo extends MessageBasicInfo
{

  /** @var string[] */
  public $keys;
  /** @var string */
  public $tag;
  /** @var string */
  public $body;
  /** @var array */
  public $properties;
  /** @var string */
  public $msgHandle;
  /** @var string */
  public $bodyCRC;
  /** @var string */
  public $bornHost;
  /** @var string */
  public $bornTimeStamp;
  /** @var string */
  public $storeTimeStamp;
  /** @var string */
  public $reconsumeTimes;

  /**
   * @param array $msgJson
   */
  public function __construct(array $msgJson = [])
  {
    parent::__construct(
      ArrayUtils::getArrayAttribute($msgJson, ["topic"]),
      ArrayUtils::getArrayAttribute($msgJson, ["queueId"]),
      ArrayUtils::getArrayAttribute($msgJson, ["queueOffset"]),
      ArrayUtils::getArrayAttribute($msgJson, ["msgId"])
    );

    $this->keys           = ArrayUtils::getArrayAttribute($msgJson, ["keys"]);
    $this->tag            = ArrayUtils::getArrayAttribute($msgJson, ["tag"]);
    $this->body           = ArrayUtils::getArrayAttribute($msgJson, ["body"]);
    $this->properties     = ArrayUtils::getArrayAttribute($msgJson, ["properties"]);
    $this->msgHandle      = ArrayUtils::getArrayAttribute($msgJson, ["msgHandle"]);
    $this->bodyCRC        = ArrayUtils::getArrayAttribute($msgJson, ["bodyCRC"]);
    $this->bornHost       = ArrayUtils::getArrayAttribute($msgJson, ["bornHost"]);
    $this->bornTimeStamp  = ArrayUtils::getArrayAttribute($msgJson, ["bornTimeStamp"]);
    $this->storeTimeStamp = ArrayUtils::getArrayAttribute($msgJson, ["storeTimeStamp"]);
    $this->reconsumeTimes = intval(
      ArrayUtils::getArrayAttribute($msgJson, ["reconsumeTimes"])
    );
  }

}


?>