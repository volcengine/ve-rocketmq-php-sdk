<?php
namespace RMQ;

use RMQ\Exception\MQInvalidArgumentException;

class Message
{
  private $topic;

  private $body;

  private $shardingKey;

  private $tag;

  private $keys = [];

  private $messageProperties = [];

  /**
   * @param string $topic
   * @param string $body
   */
  public function __construct($topic, $body)
  {
    if (empty($topic)) {
      throw new MQInvalidArgumentException("topic can not be empty`");
    }

    if (empty($body)) {
      throw new MQInvalidArgumentException("body can not be empty`");
    }

    $this->topic = $topic;
    $this->body  = $body;
  }

  /**
   * @param string $shardingKey
   */
  public function setShardingKey($shardingKey)
  {
    $this->shardingKey = $shardingKey;
  }

  /**
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }

  /**
   * @param string[] $keys
   */
  public function setKeys($keys = [])
  {
    $this->keys = $keys;
  }

  /**
   * @param string $key 
   * @param string $value 
   */
  public function putProperty($key, $value)
  {
    $this->messageProperties[$key] = $value;
  }

  /**
   * @param int $seconds 
   */
  public function delayAfter($seconds)
  {
    $this->messageProperties["__DELAY_AFTER"] = "$seconds";
  }

  /**
   * @param int $timeStamp 
   */
  public function delayAt($timeStamp)
  {
    $this->messageProperties["__DELAY_AT"] = "$timeStamp";
  }

  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }

  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * @return string
   */
  public function getShardingKey()
  {
    return $this->shardingKey;
  }

  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }

  /**
   * @return string[]
   */
  public function getKeys()
  {
    return $this->keys;
  }

  /**
   * @return array
   */
  public function getMessageProperties()
  {
    return $this->messageProperties;
  }

}

?>