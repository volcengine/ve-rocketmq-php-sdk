<?php
namespace RMQ;

use RMQ\Common\ExceptionUtils;
use RMQ\Exception\MQInvalidArgumentException;
use RMQ\Exception\MQRequestException;
use RMQ\Exception\MQTokenTimeoutException;
use RMQ\Model\MessageBasicInfo;
use RMQ\RequestParams\OpenParams;
use RMQ\RequestParams\PublishParams;
use RMQ\RequestResponse\PublishResponse;
use RMQ\Worker\Worker;
use RMQ\Worker\WorkerConstant;
use RMQ\Message;
use RuntimeException;

class Producer extends Worker
{
  /**
   * @param Client $mqClient
   */
  public function __construct(Client $mqClient)
  {
    if (empty($mqClient)) {
      throw new MQInvalidArgumentException("please pass the Client referenced by Producer");
    }

    parent::__construct($mqClient, WorkerConstant::WORKER_TYPE_PRODUCER);
  }

  public function open()
  {
    $clientVersion = $this->mqClient->getClientVersion();
    $params        = new OpenParams(WorkerConstant::WORKER_TYPE_PRODUCER, $clientVersion);
    $this->_open($params);
  }

  public function close()
  {
    $this->_close();
  }

  /**
   * @param Message $message
   * @return MessageBasicInfo
   */
  public function publishMessage(Message $message)
  {
    if (empty($message)) {
      throw new MQInvalidArgumentException("please pass the message to be sent");
    }

    try {
      $params   = new PublishParams($this->clientToken, $message);
      $resp     = $this->mqClient->_request($params->method(), $params->pathname(), $params->requestBody());
      $respData = new PublishResponse($resp);

      return new MessageBasicInfo($respData->topic, $respData->queueId, $respData->queueOffset, $respData->msgId);
    } catch (RuntimeException $e) {
      $hasToken = !empty($this->clientToken);
      if (ExceptionUtils::shouldTrowTokenTimeoutException($hasToken, $e)) {
        throw new MQTokenTimeoutException("token timeout");
      }

      throw $e;
    }
  }

}
?>