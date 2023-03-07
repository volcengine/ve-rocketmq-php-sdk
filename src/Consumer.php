<?php
namespace RMQ;

use RMQ\Common\ArrayUtils;
use RMQ\Common\ExceptionUtils;
use RMQ\Constants\ClientConstants;
use RMQ\Constants\ConsumerOptions;
use RMQ\Exception\MQInvalidArgumentException;
use RMQ\Exception\MQTokenTimeoutException;
use RMQ\Model\MessageInfo;
use RMQ\RequestParams\ACKParams;
use RMQ\RequestParams\ConsumeParams;
use RMQ\RequestParams\OpenParams;
use RMQ\RequestResponse\ACKResponse;
use RMQ\RequestResponse\ConsumeResponse;
use RMQ\Worker\Worker;
use RMQ\Worker\WorkerConstant;
use RuntimeException;

class Consumer extends Worker
{
  /** Subscribed topics and tag */
  private $subscriptions = [];
  /** The ID of consumer group*/
  private $groupId;

  private $maxMessageNumber;

  private $maxWaitTime;

  /**
   * @param Client $mqClient
   * @param string $groupId
   * @param array $config
   */
  public function __construct(Client $mqClient, $groupId, array $config = [])
  {
    if (empty($mqClient)) {
      throw new MQInvalidArgumentException("please pass the Client referenced by Consumer");
    }

    if (empty($groupId)) {
      throw new MQInvalidArgumentException("groupId is necessary");
    }

    parent::__construct($mqClient, WorkerConstant::WORKER_TYPE_CONSUMER);

    $this->groupId          = $groupId;
    $this->maxMessageNumber = ArrayUtils::getArrayAttribute($config, [ConsumerOptions::MAX_MESSAGE_NUMBER], ClientConstants::DEFAULT_MAX_MESSAGE_NUMBER);
    $this->maxWaitTime      = ArrayUtils::getArrayAttribute($config, [ConsumerOptions::MAX_WAIT_TIME], ClientConstants::DEFAULT_MAX_WAIT_TIME);
  }

  /**
   * @param string $topic topic name.
   * @param string $tag tag of message.
   */
  public function subscribe($topic, $tag = "")
  {
    $this->subscriptions[$topic] = $tag;
  }

  public function open()
  {
    if (sizeof($this->subscriptions) == 0) {
      throw new MQInvalidArgumentException("consumer open failed: no topic subscribed");
    }
    $clientVersion = $this->mqClient->getClientVersion();
    $params        = new OpenParams(WorkerConstant::WORKER_TYPE_CONSUMER, $clientVersion);
    $params->setGroupId($this->groupId);
    $params->setSubscriptions($this->subscriptions);

    $this->_open($params);
  }

  public function close()
  {
    $this->_close();
  }

  /**
   * @return MessageInfo[]
   */
  public function consumeMessage()
  {
    try {
      $params   = new ConsumeParams($this->clientToken, $this->groupId, $this->maxMessageNumber, $this->maxWaitTime);
      $resp     = $this->mqClient->_request($params->method(), $params->pathname(), $params->requestBody());
      $respData = new ConsumeResponse($resp);

      return $respData->messages;
    } catch (RuntimeException $e) {
      if (ExceptionUtils::shouldTrowTokenTimeoutException(!empty($this->clientToken), $e)) {
        throw new MQTokenTimeoutException("token timeout");
      }

      throw $e;
    }

  }

  /**
   * @param string[] $acks MessageHandles of successfully consumed messages.
   * @param string[] $nacks MessageHandles of unsuccessfully consumed messages.
   * @return string[] 
   */
  public function ackMessages($acks, $nacks = [])
  {
    try {
      $params   = new ACKParams($this->clientToken, $this->groupId, $acks, $nacks);
      $resp     = $this->mqClient->_request($params->method(), $params->pathname(), $params->requestBody());
      $respData = new ACKResponse($resp);

      return $respData->failedHandles;
    } catch (RuntimeException $e) {
      if (ExceptionUtils::shouldTrowTokenTimeoutException(!empty($this->clientToken), $e)) {
        throw new MQTokenTimeoutException("token timeout");
      }

      throw $e;
    }

  }

}
?>