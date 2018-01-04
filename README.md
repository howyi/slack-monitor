# slack-monitor
Slack Real-time monitoring tool

## Start monitoring
```php
// Return the greeting bot

$monitor = new \ServiceMonitor\Slack\SlackMonitor(getenv('SLACK_TOKEN'));

$event = new class extends \ServiceMonitor\Slack\SlackEvent
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

        $this->commander->execute('chat.postMessage', [
            'channel' => $value['channel'],
            'text'    => "Hello, {$value['user']}!"
        ]);
    }
};

$monitor->setEvent($event);
$monitor->start();
```
