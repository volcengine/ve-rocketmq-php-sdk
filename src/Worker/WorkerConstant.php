<?php
namespace RMQ\Worker;

class WorkerConstant
{
  // worker type
  const WORKER_TYPE_PRODUCER = "producer";
  const WORKER_TYPE_CONSUMER = "consumer";


  const CONNECTABLE_STATUS = [
    WorkerStatus::initialized,
    WorkerStatus::closed,
    WorkerStatus::connectFailed,
  ];

  const CLOSEABLE_STATUS = [
    WorkerStatus::connected,
    WorkerStatus::closeFailed,
  ];

}

?>