<?php

namespace ServiceMonitor\Slack;

use ServiceMonitor\EventInterface;

abstract class SlackEvent implements EventInterface
{
    protected $self;
    protected $team;

    public function set(array $self, array $team): void
    {
        $this->self = $self;
        $this->team = $team;
    }
}
