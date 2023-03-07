<?php
namespace RMQ\Constants;

class ClientConstants
{
  // ------ common -------

  /** Client Version */
  const VERSION = "0.0.2";

  /** Service Name */
  const SERVICE_NAME = "rocketmq";

  /** Region */
  const REGION = "all";

  // ------ default value -------

  const DEFAULT_SESSION_TIMEOUT = 60;

  const DEFAULT_MAX_MESSAGE_NUMBER = 10;

  const DEFAULT_MAX_WAIT_TIME = 3000;
}

?>