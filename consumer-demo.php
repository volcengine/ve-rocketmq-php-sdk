<?php
require('./vendor/autoload.php');

use RMQ\Exception\MQTokenTimeoutException;
use RMQ\Client;


class ConsumerDemo
{
    private $endpoint = "";
    private $accessKey = "";
    private $secretKey = "";
    private $groupId = "";
    private $client;
    private $consumer;

    public function __construct()
    {
        $this->client   = new Client($this->endpoint, $this->accessKey, $this->secretKey);
        $this->consumer = $this->client->createConsumer($this->groupId, [
            // 每次调用consumeMessage最多拉取12条消息
            "max_message_number" => 12,
            // 在消息达到max_message_number之前的最大等待时长
            "max_wait_time"      => 3000
        ]);
        $this->consumer->subscribe("topic_name");
    }

    public function run()
    {
        $this->consumer->open();

        while (true) {
            try {
                // 拉取消息
                $messages    = $this->consumer->consumeMessage();
                $acksHandles = [];
                foreach ($messages as $msg) {
                    $body = $msg->body;
                    echo "message bode: $body \n";
                    array_push($acksHandles, $msg->msgHandle);
                }

                // 确认消息的消费情况
                // ackMessages第一个参数是确认消费成功的消息的msgHandle
                // ackMessages第二个参数是确认消费失败的消息的msgHandle
                $this->consumer->ackMessages($acksHandles, []);
            } catch (MQTokenTimeoutException $e) {
                // token失效的情况需要重连
                $this->consumer->open();
            } catch (RuntimeException $e) {
                // 其他错误
                echo $e;
            }
        }

    }

}


$demo = new ConsumerDemo();

$demo->run();

?>