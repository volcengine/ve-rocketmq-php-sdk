<?php
namespace RMQ\RequestParams;

class ACKParams extends BaseParams
{
  private $groupId;
  private $acks;
  private $nacks;

  /**
   * @param string $clientToken
   * @param string $groupId
   * @param string[] $acks
   * @param string[] $nacks
   */
  public function __construct($clientToken, $groupId, $acks, $nacks = [])
  {
    parent::__construct($clientToken);

    $this->groupId = $groupId;
    $this->acks    = $acks;
    $this->nacks   = $nacks;
  }

  public function method()
  {
    return "DELETE";
  }

  public function pathname()
  {
    $groupId = urlencode($this->groupId);
    return "/v1/group/$groupId/msghandles";
  }

  public function requestBody()
  {
    $body = [
      "clientToken" => $this->clientToken,
      "acks"        => $this->acks,
      "nacks"       => $this->nacks,
    ];

    return $body;
  }


}

?>