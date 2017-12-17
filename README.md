# slack-monitor
Slack Real-time monitoring tool

## Start monitoring
```php
$monitor = new \ServiceMonitor\Slack\SlackMonitor(getenv('SLACK_TOKEN'));

$event = new class extends \ServiceMonitor\Slack\SlackEvent {
    public function isExecutable(array $value): bool { return true; }
    // All events execute

    public function execute(array $value): void
    {
        echo("New Event has arrived :)" . PHP_EOL);
        var_dump($value);
    }
};

$monitor->setEvent($event);
$monitor->start();
```
