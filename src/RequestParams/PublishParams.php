<?php
namespace RMQ\RequestParams;

use RMQ\Message;

class PublishParams extends BaseParams
{

  private $message;

  public function __construct($clientToken, Message $message)
  {
    parent::__construct($clientToken);
    $this->message = $message;
  }

  public function method()
  {
    return "POST";
  }

  public function pathname()
  {
    return "/v1/messages";
  }

  public function requestBody()
  {
    $message     = [
      "topic" => $this->message->getTopic(),
      "body"  => $this->message->getBody(),
    ];
    $tag         = $this->message->getTag();
    $shardingKey = $this->message->getShardingKey();
    $keys        = $this->message->getKeys();
    $properties  = $this->message->getMessageProperties();



    if (!empty($tag)) {
      $message["tag"] = $tag;
    }

    if (!empty($shardingKey)) {
      $message["shardingKey"] = $shardingKey;
    }

    $message["keys"]       = sizeof($keys) > 0 ? $keys : [];
    $message["properties"] = sizeof($properties) > 0 ? $properties : new \stdClass();


    return [
      "clientToken" => $this->clientToken,
      "message"     => $message,
    ];
  }
}

?>