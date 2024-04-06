<?php

namespace Isobar\LineBot\Service;

class Messages
{
    /**
     * @var \LINE\LINEBot\HTTPClient\CurlHTTPClientFactory
     */
    private $curlHTTPClientFactory;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var \LINE\LINEBotFactory
     */
    private $botFactory;
    /**
     * @var \LINE\LINEBot\MessageBuilder\TextMessageBuilderFactory
     */
    private $textMessageBuilderFactory;

    public function __construct(
        \LINE\LINEBot\HTTPClient\CurlHTTPClientFactory $curlHTTPClientFactory,
        \LINE\LINEBotFactory $botFactory,
        \LINE\LINEBot\MessageBuilder\TextMessageBuilderFactory $textMessageBuilderFactory,
        Config $config
    ) {
        $this->curlHTTPClientFactory = $curlHTTPClientFactory;
        $this->botFactory = $botFactory;
        $this->config = $config;
        $this->textMessageBuilderFactory = $textMessageBuilderFactory;
    }

    public function pushMessage($to, $message)
    {
        $bot = $this->initBot();
        $textMessageBuilder = $this->textMessageBuilderFactory->create(['text' => $message]);
        $result = $bot->pushMessage($to, $textMessageBuilder);

        return $result->getHTTPStatus() . ' ' . $result->getRawBody();
    }

    protected function initBot()
    {
        $chanelAccessToken = $this->config->getChanelAccessToken();
        $httpClient = $this->curlHTTPClientFactory->create(['channelToken' => $chanelAccessToken]);

        $chanelSecret= $this->config->getChanelSecret();
        $bot = $this->botFactory->create(['httpClient' => $httpClient, 'args' => ['channelSecret' => $chanelSecret]]);

        return $bot;
    }
    public function replayMessage($replyToken, $message)
    {
        $bot = $this->initBot();
        $textMessageBuilder = $this->textMessageBuilderFactory->create(['text' => $message]);

        $result = $bot->replyMessage($replyToken, $textMessageBuilder);

        return $result->getHTTPStatus() . ' ' . $result->getRawBody();
    }
}
