<?php

namespace ServiceMonitor\Slack;

use Devristo\Phpws\Client\WebSocket;
use React\EventLoop\Factory;
use ServiceMonitor\Monitor;
use Zend\Log\Logger;

class SlackMonitor extends Monitor
{
    private $token;
    private $self;
    private $team;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function start(): void
    {
        $url = 'https://slack.com/api/rtm.connect?' . http_build_query(['token' => $this->token]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($ch);
        if ($body === false) {
            throw new \Exception("Request error:" . curl_error($ch));
        }
        curl_close($ch);
        $response = json_decode($body, true);
        if (is_null($response)) {
            throw new \Exception("Response perse error: $body");
        }
        if (isset($response['error'])) {
            throw new \Exception($response['error']);
        }

        foreach ($this->events as $event) {
            $event->set($response['self'], $response['team']);
        }

        $loop = Factory::create();
        $client = new WebSocket($response['url'], $loop, new Logger());

        $client->on("message", function ($message) {
            $value = json_decode($message->getData(), true);
            $this->execute($value);
        });

        $client->open();
        $loop->run();
    }
}
