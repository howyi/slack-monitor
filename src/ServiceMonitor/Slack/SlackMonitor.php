<?php

namespace ServiceMonitor\Slack;

use Devristo\Phpws\Client\WebSocket;
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
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
        $curl = new Curl($url);
        $body = $curl->exec();
        if ($body === false) {
            throw new \Exception("Request error: " . $curl->error());
        }
        $curl->close();
        $response = json_decode($body, true);
        if (is_null($response)) {
            throw new \Exception("Response perse error: $body");
        }
        if (isset($response['error'])) {
            throw new \Exception($response['error']);
        }

        $interactor = new CurlInteractor;
        $interactor->setResponseFactory(new SlackResponseFactory);

        $commander = new Commander($this->token, $interactor);

        foreach ($this->events as $event) {
            $event->set($response['self'], $response['team'], $commander);
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
