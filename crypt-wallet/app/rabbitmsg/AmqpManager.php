<?php 

namespace App\rabbitmsg;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpManager
{

    /**
     * The application instance.
     */
    private $app;

    /**
     * AmqpConnection instance
     */
    private $connections;

    public function __construct ($app) 
    {
        $this->app = $app;
    }

    public function getConn($connection)
    {
        return $this->getConnection($connection);
    }

    public function getChann($connection)
    {
        return $this->getChannel($connection);
    }
    
    protected function getConfig($connection)
    {
        return $this->app['config']["msgqueue.connections.{$connection}"];
    }

    protected function getConnection ($connection) 
    {
        return $this->connections[$connection] ?? ($this->connections[$connection] = $this->connect($this->getConfig($connection)));
    }

    protected function getChannel($connection) 
    {
        return $this->getConnection($connection)->channel();
    }

    protected function connect(array $config) : AMQPStreamConnection
    {
        return new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
    }

    public function publish($channel, $exchange, AMQPMessage $message)
    {
        $channel->basic_publish($message, $exchange);
    }

    public function consume($channel, $queue, $consumerTag, $no_local, $no_ack, $exclusive, $nowait, $callback)
    {
        $channel->basic_consume($queue, $consumerTag, $no_local, $no_ack, $exclusive, $nowait, $callback);
    }

    public function exchangeDeclare($channel, $exchange, $type, $passive, $durable, $auto_delete) 
    {
        $channel->exchange_declare($exchange, $type, $passive, $durable, $auto_delete);
    }

    public function queueDeclare($channel, $queue, $passive, $durable, $exclusive, $auto_delete) 
    {
        $channel->queue_declare($queue, $passive, $durable, $exclusive, $auto_delete);
    }

    public function queueBind($channel, $queue, $exchange) 
    {
        $channel->queue_bind($queue, $exchange);
    }

    public function queueBindMany($channel, array $queues, $exchange) 
    {
        foreach ($queues as $key => $queue) {
            $channel->queue_bind($queue, $exchange);
        }
    }

    public function closeChannel ($channel) 
    {
        $channel->close();
    }

    public function closeConnection ($connection) 
    {
        $this->connections[$connection]->close();
    }

    public function close($connection, $channel) 
    {
        $this->closeChannel($channel);
        $this->closeConnection($connection);
    }
}