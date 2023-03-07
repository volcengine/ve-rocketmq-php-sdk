<?php

namespace RMQ\Constants;

class RequestErrorCode
{
  const Success = "0";
  const InvalidParameter = "100000";
  const TopicNotExist = "100001";
  const GroupNotExist = "100002";
  const UnsupportedClientType = "100003";
  const NoAccessPermissionForTopic = "100004";
  const NoAccessPermissionForGroup = "100005";
  const InvalidProperties = "100006";
  const ClientNotFound = "100007";
  const ReqBodyNotExist = "100008";
  const InternalServerError = "100009";
  const ClientHasTaskRunning = "100010";
  const ReqBodyTooLarge = "100011";
  const AckMessageError = "100012";
  const UnknownError = "100013";
  const InvalidPollRequestParameter = "100018";
  const ProducerGroupHasAlreadyExist = "100019";
  const InvalidDelayTimeLevel = "100020";
  const RetryErrorCodeStart = "300000";
  const TooManyWaitAckMessage = "300001";
  const TooManyOpenRequest = "300002";
  const TooManyAckRequest = "300003";
  const TooManySendRequest = "300004";
}

?>