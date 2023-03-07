<?php
namespace RMQ\Model;

class MessageBasicInfo
{
  /** @var string  */
  public $topic;
  /** @var string  */
  public $queueId;
  /** @var string  */
  public $queueOffset;
  /** @var string  */
  public $msgId;

  /**
   * @param string $topic
   * @param string $queueId
   * @param string $queueOffset
   * @param string $msgId
   */
  public function __construct($topic, $queueId, $queueOffset, $msgId)
  {
    $this->topic       = $topic;
    $this->queueId     = $queueId;
    $this->queueOffset = $queueOffset;
    $this->msgId       = $msgId;
  }
}

?>