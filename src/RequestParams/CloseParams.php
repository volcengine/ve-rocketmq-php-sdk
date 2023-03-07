<?php
namespace RMQ\RequestParams;

class CloseParams extends BaseParams
{

  public function __construct($clientToken)
  {
    parent::__construct($clientToken);
  }

  public function method()
  {
    return "DELETE";
  }

  public function pathname()
  {
    $basePath = "/v1/clients";
    return "$basePath/$this->clientToken";
  }

  public function requestBody()
  {
    $body = [
      "clientToken" => $this->clientToken,
      "properties"  => $this->getPropertiesObject(),
    ];
    return $body;
  }


}

?>