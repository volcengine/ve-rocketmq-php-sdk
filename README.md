# RocketMQ PHP SDK

火山引擎消息队列 RocketMQ版PHP SDK 是基于 Http-Proxy 的RocketMQ客户端。 该SDK可通过RocketMQ实例的Http Proxy 接入点连接实例，实现消息的生产与消费。

# 安装

```shell
composer require volcengine/ve-rocketmq-php-sdk
```

# 快速开始

## 创建客户端。

初始化一个RocketMQ客户端需要准备好火山引擎RocketMQ实例的Http Proxy接入点、accessKey和secretKey。

```php
use RMQ\Client;

//  HTTP Proxy 接入点
$endpoint = ""; 
// 密钥
$accessKey = ""; 
$secretKey = "";

// 实例化客户端
$client = new Client($endpoint, $accessKey, $secretKey);
```

## 生产

### 创建生产者

调用client实例的 `createProducer()` 方法即可创建一个生产者实例。

```php
// 创建一个生产者
$producer = $client->createProducer();
```

### 创建消息

一条消息拥有很多属性topic、body、tag、key等，可以使用 `Message` 实例化一个消息的信息对象并可在这个对象上设置这些属性。

```php
use RMQ\Message;

// 目标topic
$topic ="topic_name"; 
// 消息的内容
$messageContent = "content." 

// 实例化一个消息
$msg = new Message($topic, $messageContent);
// 设置消息的tag值
$msg->setTag("tag_a");
// 设置ShardingKey
$msg->setShardingKey("my_key");
// 设置自定义属性
$msg->putProperty("property_name", "test");
```

### 生产消息

调用producer实例的 `publishMessage()` 方法就能发布一条消息。在发布消息前还需要调用 `open()` 方法在服务端开启一个生产者实例， 在不需要发送消息时可以调用 `close()` 方法销毁。

```php
$producer->open();

$msg = new Message("topic_name", "hello!");
$messageInfo = $producer->publishMessage($msg);
$producer->close();

var_dump(messageInfo);
```
## 消费

### 创建消费者

调用client实例的 `createConsumer()` 方法即可创建一个消费者实例, 创建消费者时必须指定消费者的GroupID。

```php
$groupID = ""; // 消费组ID
 
$consumer = $client->createConsumer($groupID, [
  // 每次调用consumeMessage最多拉取12条消息
  "max_message_number" => 12,
  // 在消息达到max_message_number之前的最大等待时长（单位ms）
  "max_wait_time"      => 3000
]);
```

### 消费消息

调用消费者的 `consumeMessage()` 能拉取一批消息。在拉取消息并被使用后，需要调用 `ackMessages()` 对消息的消费状态进行确认，未被确认或确认消费失败的消息都会被重复消费。

```php
use RMQ\Model\MessageInfo;

$consumer = $client->createConsumer($groupID);
// 订阅topic_a 全部消息1
$consumer->subscribe("topic_a");
// 订阅topic_b tag为A的消息
$consumer->subscribe("topic_b", "A");

$consumer->open();

// 拉取消息
$messages = $consumer->consumeMessage();

$acksHandles = [];
foreach ($messages as $msg) {
    $body = $msg->body;
    echo "message bode: $body \n";
    array_push($acksHandles, $msg->msgHandle);
}
// 确认消息的消费情况
// ackMessages第一个参数是确认消费成功的消息的msgHandle
// ackMessages第二个参数是确认消费失败的消息的msgHandle
$consumer->ackMessages($acksHandles, []);

$consumer->close();
```

# 进阶指引

## 持续生产消息

服务端会对每一个客户端创建一个生产者实例，在客户端生产频率较低时，可能会出现服务端生产者实例被释放导致生产消息失败的情况。

```php
$producer->open();
// 在open后等待60秒
sleep(60);
// 下面的方法调用会失败，因为服务端的生产者实例已超时被销毁掉
$producer->publishMessage($msg);
```

所以在持续生产消息时需要捕获这类异常并重新调用 `open()` 法重新在服务端开启一个生产者实例，SDK 提供了一个专门用来捕获该类错误的Exception在`RMQ\Exception\MQTokenTimeoutException` 。如下demo，对部分消息等待一个很长的时间，这些消息发送时就会捕获到超时错误。

```php
use RMQ\Exception\MQTokenTimeoutException;

for ($i = 0; $i < 10; $i++) {
  if ($i % 2 == 0) {
    sleep(60 * 10); // 偶数消息等待10分钟
  }
  
  $message = new Message("topic_name", "hello!");
  try {
    // 发送消息
    $producer->publishMessage($message);
  } catch (MQTokenTimeoutException $e) {
    // token失效的情况需要重连
    $producer->open();
    // 对消息重发
    $producer->publishMessage($message);
  } catch (RuntimeException $e) {
    // 其他错误情况
    echo $e . "\n";
  }
}
```

## 持续消费消息

持续消息实际上就是一个轮询不断拉取消息，如果每次拉取消息的间隔过长也可能出现超时的情况，所以也需要捕获超时错误并重新调用 `open()` 方法。

```php
$consumer->open();

while (true) {
  try {
    // 拉取消息
    $messages    = $consumer->consumeMessage();
    $acksHandles = [];
    foreach ($messages as $msg) {
        $body = $msg->body;
        echo "message bode: $body \n";
        array_push($acksHandles, $msg->msgHandle);
    }
    // 确认消费状态
    $consumer->ackMessages($acksHandles, []);
  } catch (MQTokenTimeoutException $e) {
    // token失效的情况需要重连
    $consumer->open();
  } catch (RuntimeException $e) {
    // 其他错误 
    echo $e;
  }
}
```

## 延时投递消息

`Message` 类有 `setDelayLevel()` 方法可设置消息的延时属。可设置1-18等级.

```php
$msg2 = new Message("topic_name", "content");

$msg2->setDelayLevel(5)
```
