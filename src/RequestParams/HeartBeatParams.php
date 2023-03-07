<?php
namespace RMQ\RequestParams;

class HeartBeatParams extends BaseParams
{

  public function __construct($clientToken)
  {
    parent::__construct($clientToken);
  }

  public function method()
  {
    return "POST";
  }

  public function pathname()
  {
    return "/v1/heartbeats";
  }

  public function requestBody()
  {
    $body = [
      "clientToken" => $this->clientToken
    ];
    return $body;
  }


}

?>