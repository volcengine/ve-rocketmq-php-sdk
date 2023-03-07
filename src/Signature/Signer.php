<?php
namespace RMQ\Signature;

use RMQ\Constants\ClientConstants;

class Signer
{
  private $REGION = ClientConstants::REGION;
  private $SERVICE_NAME = ClientConstants::SERVICE_NAME;
  private $method = "";
  private $pathname = "";
  private $headers = [];
  private $body = [];

  public function __construct($method, $pathname, $headers, $body)
  {
    $this->method   = $method;
    $this->pathname = $pathname;
    $this->headers  = $headers;
    $this->body     = $body;
  }

  /**
   * Add the singed headers to the request
   * @param string $accessKey
   * @param string $secretKey
   * @return void
   */
  public function addAuthorization($accessKey, $secretKey)
  {
    $dateTime = $this->getDateTimeNow();

    $this->headers[SignatureConstant::DATE_HEADER]           = $dateTime;
    $this->headers[SignatureConstant::CONTENT_SHA256_HEADER] = $this->getBodySha256();
    $this->headers[SignatureConstant::AUTH_HEADER]           = $this->getAuthorization($accessKey, $secretKey, $dateTime);
  }

  /**
   * get the signed headers
   * @return array|mixed
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  public function getBody()
  {
    return $this->body;
  }

  private function getDateTimeNow()
  {
    $time = gmdate(DATE_ISO8601);
    $time = preg_replace("/\+\d{4}$/", "Z", $time);
    return preg_replace("/[:\-]|\.\d{3}/", "", $time);
  }

  private function getBodySha256()
  {
    $bodyStr = json_encode($this->body);
    return hash("sha256", $bodyStr);
  }

  private function getAuthorization($accessKey, $secretKey, $dateTime)
  {
    $credentialString = $this->getCredentialString($dateTime);
    $signatureStr     = $this->getSignature($secretKey, $dateTime, $credentialString);
    $parts            = [
      sprintf("%s Credential=%s/%s", SignatureConstant::ALGORITHM, $accessKey, $credentialString),
      sprintf("SignedHeaders=%s", $this->getSignedHeaders()),
      sprintf("Signature=%s", $signatureStr)
    ];

    return join(", ", $parts);
  }

  private function getCredentialString($dateTime)
  {
    $credentials      = [substr($dateTime, 0, 8), $this->REGION, $this->SERVICE_NAME, SignatureConstant::V4_IDENTIFIER];
    $credentialString = join("/", $credentials);
    return $credentialString;
  }

  private function getSignedHeaders()
  {
    $keys = [];
    foreach ($this->headers as $k => $v) {
      $k = strtolower($k);
      if (in_array($k, SignatureConstant::UN_SIGNABLE_HEADERS)) {
        continue;
      }
      array_push($keys, $k);
    }

    sort($keys);
    return join(";", $keys);
  }

  private function getSignature($secretKey, $dateTime, $credentialString)
  {
    // get the signingKey
    $date       = substr($dateTime, 0, 8);
    $kDate      = hash_hmac("sha256", $date, $secretKey, true);
    $kRegion    = hash_hmac("sha256", $this->REGION, $kDate, true);
    $kServise   = hash_hmac("sha256", $this->SERVICE_NAME, $kRegion, true);
    $signingKey = hash_hmac("sha256", SignatureConstant::V4_IDENTIFIER, $kServise, true); // result

    // get the content to be signed
    $contentToBeSigned = $this->getStringToBeSigned($dateTime, $credentialString);

    return hash_hmac("sha256", $contentToBeSigned, $signingKey);
  }

  private function getStringToBeSigned($dateTime, $credentialString)
  {
    $canonicalString = join("\n", [
      // method
      strtoupper($this->method),
      //path
      $this->pathname,
      // query
      "",
      //headers content
      sprintf("%s\n", $this->getCanonicalHeaders()),
      // signed headers name
      $this->getSignedHeaders(),
      // body sha256
      $this->getBodySha256(),
    ]);

    $contentParts = [
      SignatureConstant::ALGORITHM,
      $dateTime,
      $credentialString,
      hash("sha256", $canonicalString)
    ];

    return join("\n", $contentParts);
  }

  private function getCanonicalHeaders()
  {

    $sortedHeaders = [];
    // 把原始header名称放入数组
    foreach ($this->headers as $k => $v) {
      array_push($sortedHeaders, $k);
    }
    // header 按小写排序
    usort($sortedHeaders, function ($a, $b) {
      return (strtolower($a) < strtolower($b)) ? -1 : 1;
    });

    $parts = [];

    foreach ($sortedHeaders as $header) {
      $lowerHeader = strtolower($header);
      $headerValue = $this->headers[$header];

      if (in_array($lowerHeader, SignatureConstant::UN_SIGNABLE_HEADERS)) {
        continue;
      }
      // 组装header:value
      $headerValue = preg_replace("/\s+/", " ", $headerValue);
      $headerValue = preg_replace("/^\s+|\s+$/", "", $headerValue);

      $kvPart = sprintf("%s:%s", $lowerHeader, $headerValue);
      array_push($parts, $kvPart);
    }

    return join("\n", $parts);
  }

}

?>