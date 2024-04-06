<?php

namespace Isobar\LineBot\Service\Webhook\Line\EchoBot;

use Isobar\LineBot\Service\Config;

class Setting
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getSetting()
    {
        $chanelAccessToken = $this->config->getChanelAccessToken();
        $chanelSecret = $this->config->getChanelSecret();
        return [
            'settings' => [
                'displayErrorDetails' => true, // set to false in production

                'logger' => [
                    'name' => 'slim-app',
                    'path' => '/var/logs/linebot.log',
                ],
                'bot' => [
                    'channelToken' => $chanelAccessToken,
                    'channelSecret' => $chanelSecret,
                ]
            ],
        ];
    }
}
