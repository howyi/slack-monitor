<?php

namespace ServiceMonitor\Slack;

use ServiceMonitor\EventInterface;

class SampleEvent extends SlackEvent
{
    public function isExecutable(array $value): bool
    {
        if (!isset($value['type']) or !isset($value['text'])) {
            return false;
        }
        return (('message' === $value['type']) and ('hello' === $value['text']));
    }

    public function execute(array $value): void
    {
        echo("User:{$value['user']} greeted :)" . PHP_EOL);
    }
}
