<?php
  // Worker
  include_once('../config.php');

  use PhpAmqpLib\Connection\AMQPStreamConnection;
  use PhpAmqpLib\Message\AMQPMessage;

  $rabbitmq = parse_url(getenv('CLOUDAMQP_URL'));
  $rabbitmq_port = isset($rabbitmq['port']) ? $rabbitmq['port'] : 5672;
  $rabbitmq_vhost = substr($rabbitmq['path'], 1) ?: '/';
  $connection = new AMQPStreamConnection($rabbitmq['host'], $rabbitmq_port, $rabbitmq['user'], $rabbitmq['pass'], $rabbitmq_vhost);
  $channel = $connection->channel();

  $channel->queue_declare('task_queue', false, true, false, false);

  $callback = function($msg) {

    $output = generateAPreview($msg);

  };

  //getYoutubePreview($previews_to_check[0]);

  $channel->basic_qos(null, 1, null);
  $channel->basic_consume('task_queue', '', false, false, false, false, $callback);
  // loop over incoming messages
  while(count($channel->callbacks)) {
    $channel->wait();
  }

  $channel->close();
  $connection->close();
