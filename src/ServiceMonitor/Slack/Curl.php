<?php

namespace ServiceMonitor\Slack;

/**
 * @codeCoverageIgnore
 */
class Curl
{
    private $ch;

    public function __construct(string $url)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    public function exec()
    {
        return curl_exec($this->ch);
    }

    public function error(): string
    {
        return curl_error($this->ch);
    }

    public function close(): void
    {
        curl_close($this->ch);
    }
}
