<?php

require('./vendor/autoload.php');

use RMQ\Exception\MQTokenTimeoutException;
use RMQ\Client;
use RMQ\Message;

class ConsumeDemo
{


  private $endpoint = "";

  private $accessKey = "";

  private $secretKey = "";

  private $client;

  private $producer;

  public function __construct()
  {
    $this->client   = new Client($this->endpoint, $this->accessKey, $this->secretKey);
    $this->producer = $this->client->createProducer();
  }

  /**
   * 发送一条消息后关闭
   */
  public function sendMessageOnce(Message $msg)
  {
    $this->producer->open();
    $messageInfo = $this->producer->publishMessage($msg);
    $this->producer->close();
    return $messageInfo;
  }


  /**
   * 持续发送n条消息
   */
  public function testRun($msgCount)
  {
    $this->producer->open();

    $i = 1;
    while ($i < $msgCount) {
      try {
        $topic_name      = "topic_name";
        $message_content = "message content $i";
        $message         = new Message($topic_name, $message_content);
        // 发送消息
        $info = $this->producer->publishMessage($message);
        echo "消息已经写入队列：" . $info->queueId . "\n";
      } catch (MQTokenTimeoutException $e) {
        // token失效的情况需要重连
        $this->producer->open();
      } catch (RuntimeException $e) {
        // 其他错误情况
        echo $e . "\n";
      } finally {
        // 可进行重试或落库等操作
      }

      $i++;
    }

    $this->producer->close();

  }

}

$demo = new ConsumeDemo();

// --------发送一条消息---------
$msg = new Message("topic_name", "content");
// 设置消息的tag值
$msg->setTag("tag_a");
// 设置ShardingKey
$msg->setShardingKey("my_key");
// 设置自定义属性
$msg->putProperty("property_name", "test");

// 发送消息
$messageInfo = $demo->sendMessageOnce($msg);
// 输出消息信息
var_dump($messageInfo);


// 持续发送10条消息
$demo->testRun(10);

?>