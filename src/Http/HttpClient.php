<?php
namespace RMQ\Http;

use \GuzzleHttp\Client;
use \GuzzleHttp\RequestOptions;
use \GuzzleHttp\Exception\TransferException;
use RMQ\Exception\MQRequestException;
use RMQ\Common\ArrayUtils;

class HttpClient
{
  private $_client;

  public function __construct($address)
  {
    $this->_client = new Client([
      'base_uri' => $address
    ]);
  }

  public function sendRequest($method, $path, $headers, $body)
  {
    try {
      $resp    = $this->_client->request($method, $path, [
        RequestOptions::HEADERS => $headers,
        RequestOptions::JSON => $body,
      ]);
      $bodyObj = json_decode($resp->getBody(), true);

      return ArrayUtils::getArrayAttribute($bodyObj, ["result"], []);
    } catch (TransferException $e) {
      $httpMessage = $e->getMessage();
      $httpCode    = $e->getCode();
      $respData    = [];

      if ($e->hasResponse()) {
        $respData = json_decode($e->getResponse()->getBody(), true);
      }

      throw new MQRequestException($httpMessage, $httpCode, $body, empty($respData) ? [] : $respData, $e);
    }
  }

}
?>