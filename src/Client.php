<?php
namespace RMQ;

use RMQ\Common\ArrayUtils;
use RMQ\Constants\ClientConstants;
use RMQ\Constants\ClientOptions;
use RMQ\Exception\MQInvalidArgumentException;
use RMQ\Http\HttpClient;
use RMQ\Signature\Signer;

class Client
{
  /** @var string version  */
  private $version = ClientConstants::VERSION;
  /** @var int timeout of each session (seconds)  */
  private $sessionTimeout;
  /** @var string http proxy address   */
  private $endpoint;
  /** @var string  host of endpoint */
  private $Host;
  /** @var string  accessKey of instance */
  private $accessKey;
  /** @var string  secretKey of instance */
  private $secretKey;
  /** @var HttpClient  http client */
  private $httpClient;

  /**
   * @param string $endpoint http-proxy endpoint.
   * @param string $accessKey
   * @param string $secretKey
   * @param array $config
   */
  public function __construct($endpoint, $accessKey, $secretKey, array $config = [])
  {
    if (empty($endpoint)) {
      throw new MQInvalidArgumentException("endpoint is necessary");
    }

    if (empty($accessKey)) {
      throw new MQInvalidArgumentException("accessKey is necessary");
    }

    if (empty($secretKey)) {
      throw new MQInvalidArgumentException("secretKey is necessary");
    }

    $urlInfo  = parse_url($endpoint);
    $hostname = $urlInfo["host"];
    $port     = $urlInfo["port"];

    $this->endpoint       = $endpoint;
    $this->accessKey      = $accessKey;
    $this->secretKey      = $secretKey;
    $this->sessionTimeout = ArrayUtils::getArrayAttribute($config, [ClientOptions::SESSION_TIMEOUT], ClientConstants::DEFAULT_SESSION_TIMEOUT);
    $this->Host           = empty($port) ? $hostname : "$hostname:$port";
    $this->httpClient     = new HttpClient($endpoint);
  }

  /**
   * Return a Producer Instance.
   * @return Producer
   */
  public function createProducer()
  {
    return new Producer($this);
  }

  /**
   * Return a Consumer Instance.
   * @param string $groupId
   * @param array $config
   * @return Consumer
   */
  public function createConsumer($groupId, array $config = [])
  {
    return new Consumer($this, $groupId, $config);
  }

  /**
   * Get the version of this client.
   * @return string
   */
  public function getClientVersion()
  {
    return $this->version;
  }

  /** 
   * Get the session timeout of client.
   * @return int
   * */
  public function getSessionTimeout()
  {
    return $this->sessionTimeout;
  }

  /**
   * @param string $method
   * @param string $path
   * @return array 
   */
  public function _request($method, $path, array $body)
  {
    $headers           = ["Host" => $this->Host];
    $body['requestId'] = uniqid();
    $signer            = new Signer($method, $path, $headers, $body);

    $signer->addAuthorization($this->accessKey, $this->secretKey);

    return $this->httpClient->sendRequest($method, $path, $signer->getHeaders(), $signer->getBody());
  }

}
?>