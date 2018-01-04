<?php

namespace ServiceMonitor\Slack;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class SlackMonitorTest extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testStart(): void
    {
        $monitor = new SlackMonitor('SLACK_TOKEN');

        $curl = m::mock('overload:' . Curl::class);
        $curl->expects()->exec()->andReturn(json_encode(['url' => 'resonpose_url', 'self' => [], 'team' => []]))->once();
        $curl->expects()->close()->once();

        $client = m::mock('overload:\Devristo\Phpws\Client\WebSocket');
        $client->expects()->on('message',m::type(\Closure::class))->once();

        $client->expects()->open()->once();

        $monitor->setEvent(new SampleEvent());

        $monitor->start();

        $monitor->execute(['type' => 'message', 'channel' => 'channel']);
        $monitor->execute(['type' => 'message', 'channel' => 'channel', 'text' => 'hello', 'user' => 'user']);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
  	 * @expectedException \Exception
  	 * @expectedExceptionMessage Request error: body
     */
    public function testStartFailedRequestError(): void
    {
        $monitor = new SlackMonitor('SLACK_TOKEN');

        $curl = m::mock('overload:' . Curl::class);
        $curl->expects()->exec()->andReturn(false)->once();
        $curl->expects()->error()->andReturn('body')->once();

        $monitor->start();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
  	 * @expectedException \Exception
  	 * @expectedExceptionMessage Response perse error: sushi
     */
    public function testStartFailedParseError(): void
    {
        $monitor = new SlackMonitor('SLACK_TOKEN');

        $curl = m::mock('overload:' . Curl::class);
        $curl->expects()->exec()->andReturn("sushi")->once();
        $curl->expects()->close()->once();

        $monitor->start();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
  	 * @expectedException \Exception
  	 * @expectedExceptionMessage Slack error
     */
    public function testStartFailedError(): void
    {
        $monitor = new SlackMonitor('SLACK_TOKEN');

        $curl = m::mock('overload:' . Curl::class);
        $curl->expects()->exec()->andReturn(json_encode(['error' => 'Slack error']))->once();
        $curl->expects()->close()->once();

        $monitor->start();
    }
}
