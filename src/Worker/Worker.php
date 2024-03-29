<?php
namespace RMQ\Worker;

use RMQ\Client;
use RMQ\Common\ExceptionUtils;
use RMQ\Exception\MQInvalidSecretKeyException;
use RMQ\RequestParams\OpenParams;
use RMQ\RequestParams\CloseParams;
use RMQ\RequestResponse\OpenResponse;
use RuntimeException;

class Worker
{
  /** @var string  */
  protected $workerType;
  /** @var int  */
  protected $workerStatus;
  /** @var Client  */
  protected $mqClient;
  /** @var string  */
  protected $clientToken;

  /**
   * @param Client $mqClient
   * @param string $workerType
   */
  public function __construct(Client $mqClient, $workerType)
  {
    $this->mqClient     = $mqClient;
    $this->workerType   = $workerType;
    $this->workerStatus = WorkerStatus::initialized;
  }

  /**
   * @param OpenParams $params
   */
  protected function _open(OpenParams $params)
  {
    try {
      $timeoutSecond = $this->mqClient->getSessionTimeout();
      $params->addProperties("session_timeout", "$timeoutSecond");
      $resp              = $this->mqClient->_request($params->method(), $params->pathname(), $params->requestBody());
      $respDate          = new OpenResponse($resp);
      $this->clientToken = $respDate->clientToken;
    } catch (RuntimeException $e) {
      if(ExceptionUtils::shouldTrowMQInvalidSecretKey(!empty($this->clientToken),$e)){
        throw new MQInvalidSecretKeyException('invalid secret key');
      }

      throw $e;
    }
  }

  protected function _close()
  {
    $params = new CloseParams($this->clientToken);
    $this->mqClient->_request($params->method(), $params->pathname(), $params->requestBody());
    $this->clientToken = "";
  }
}