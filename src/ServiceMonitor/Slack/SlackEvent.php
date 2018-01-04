<?php

namespace ServiceMonitor\Slack;

use Frlnc\Slack\Core\Commander;
use ServiceMonitor\EventInterface;

abstract class SlackEvent implements EventInterface
{
    protected $self;
    protected $team;
    protected $commander;

    public function set(array $self, array $team, Commander $commander): void
    {
        $this->self = $self;
        $this->team = $team;
        $this->commander = $commander;
    }
}
