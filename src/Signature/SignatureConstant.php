<?php
namespace RMQ\Signature;

final class SignatureConstant
{
  // header 相关
  const DATE_HEADER = 'X-Date';

  const CONTENT_SHA256_HEADER = 'X-Content-Sha256';

  const AUTH_HEADER = 'Authorization';

  const UN_SIGNABLE_HEADERS = [
    "authorization",
    "content-type",
    "content-length",
    "user-agent",
    "presigned-expires",
    "expect",
  ];

  // 其他常量
  const ALGORITHM = 'HMAC-SHA256';

  const V4_IDENTIFIER = 'request';

}

?>