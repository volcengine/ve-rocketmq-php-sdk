<?php
namespace RMQ\RequestParams;

class ConsumeParams extends BaseParams
{
  private $groupId;
  private $messageNumber;
  private $maxWaitTimeMs;

  public function __construct($clientToken, $groupId, $messageNumber, $maxWaitTimeMs)
  {
    parent::__construct($clientToken);

    $this->groupId       = $groupId;
    $this->messageNumber = $messageNumber;
    $this->maxWaitTimeMs = $maxWaitTimeMs;
  }

  public function method()
  {
    return "POST";
  }

  public function pathname()
  {
    $groupId = urlencode($this->groupId);
    return "/v1/group/$groupId/messages";
  }

  public function requestBody()
  {
    return [
      "clientToken"      => $this->clientToken,
      "maxMessageNumber" => $this->messageNumber,
      "maxWaitTimeMs"    => $this->maxWaitTimeMs,
    ];
  }
}

?>