<?php
namespace RMQ\Worker;

class WorkerStatus
{
  const initialized = 0;
  const connecting = 1;
  const connectFailed = 2;
  const connected = 3;
  const closing = 4;
  const closeFailed = 5;
  const closed = 6;

}


?>