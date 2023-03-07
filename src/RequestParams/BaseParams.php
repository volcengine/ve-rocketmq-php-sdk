<?php
namespace RMQ\RequestParams;

abstract class BaseParams
{
  /** 
   * @var string
   * 所有有请求都能传token，“链接请求”只在用于重连操作时需要传token，所以这个参数非必传。
   */
  protected $clientToken;
  /**
   * @var array
   * 请求的properties对象，每个请求会传入不同的字段。
   */
  protected $properties = [];

  public function __construct($clientToken = null)
  {
    $this->clientToken = $clientToken;
  }

  /**
   * @return string
   */
  abstract public function method();

  /**
   * @return string
   */
  abstract public function pathname();

  /**
   * @return array
   */
  abstract public function requestBody();

  /**
   * @param string $name
   * @param string $value
   */
  public function addProperties($name, $value)
  {
    $this->properties[$name] = $value;
  }

  /**
   * @param string $clientToken
   */
  public function setClientToken($clientToken)
  {
    $this->clientToken = $clientToken;
  }

  /** 
   * 将properties转化成能格式化成json对象的数据.
   * @return array|object
   */
  protected function getPropertiesObject()
  {
    if (sizeof($this->properties) === 0) {
      return new \stdClass();
    }
    return $this->properties;
  }

}

?>