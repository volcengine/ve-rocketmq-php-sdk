<?php
namespace RMQ;

use RMQ\Common\NumberUtils;
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
      throw new MQInvalidArgumentException("topic can not be empty");
    }

    if (empty($body)) {
      throw new MQInvalidArgumentException("body can not be empty");
    }

    $this->topic = $topic;
    $this->body  = $body;
  }

  /**
   * @param string $shardingKey
   */
  public function setShardingKey($shardingKey)
  {
    if (empty($shardingKey)) {
      throw new MQInvalidArgumentException("shardingKey can not be empty");
    }
    $this->shardingKey = $shardingKey;
  }

  /**
   * @param string $tag
   */
  public function setTag($tag)
  {
    if (empty($tag)) {
      throw new MQInvalidArgumentException("tag can not be empty");
    }
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
    if (empty($key)) {
      throw new MQInvalidArgumentException("key can not be empty");
    }

    $this->messageProperties[$key] = $value;
  }

  /**
   * @param number $level
   */
  public function setDelayLevel($level) {
    if (!NumberUtils::intCheck($level, 1, 18)) {
      throw new MQInvalidArgumentException("level must be an integer in the range of 1 to 18");
    }
    $this->putProperty("__DelayTimeLevel", "$level");
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