<?php
namespace RMQ\RequestParams;

use RMQ\Worker\WorkerConstant;

class OpenParams extends BaseParams
{
  /**
   * worker 的类型 
   * 1. WorkerConstant::WORKER_TYPE_PRODUCER
   * 1. WorkerConstant::WORKER_TYPE_CONSUMER
   */
  private $workerType;
  /**
   * client的版本号 
   * */
  private $clientVersion;
  /**
   * groupId，再注册消费者时必须传这个字段
   */
  private $groupId;
  /**
   * 消费者订阅的topic及消息tag。
   * 该array的key是topic名称，value是消息的tag，不指定tag就传空字符串。
   */
  private $subscriptions = [];

  public function __construct($workerType, $clientVersion, $clientToken = null)
  {
    parent::__construct($clientToken);
    $this->workerType    = $workerType;
    $this->clientVersion = $clientVersion;
  }

  public function method()
  {
    return "POST";
  }

  public function pathname()
  {
    return "/v1/clients";
  }

  public function requestBody()
  {
    $body = [
      "type"          => $this->workerType,
      "clientVersion" => $this->clientVersion,
      "properties"    => $this->getPropertiesObject(),
    ];

    if ($this->workerType === WorkerConstant::WORKER_TYPE_CONSUMER) {
      $body["group"]         = $this->groupId;
      $body["subscriptions"] = $this->subscriptions;
    }

    return $body;
  }

  public function setSubscriptions($subscriptions = [])
  {
    $this->subscriptions = $subscriptions;
  }

  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }

}

?>