<?php

namespace Isobar\LineBot\Service\Webhook\Line\EchoBot;

use Isobar\LineBot\Service\Config;
use LINE\LINEBot\SignatureValidator as SignatureValidator;

class Route
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var \Isobar\LineBot\Service\Messages
     */
    private $messages;

    public function __construct(Config $config, \Isobar\LineBot\Service\Messages $messages)
    {
        $this->config = $config;
        $this->messages = $messages;
    }

    public function register(\Slim\App $app)
    {
        /* ROUTES */
        $app->get('/', function ($request, $response) {
            return "Ready!";
        });

        $app->post('/', function ($request, $response) {
            if (!$this->config->isEnable()) {
                return $response->withStatus(200, 'OK');
            }
            // get request body and line signature header
            //@codingStandardsIgnoreStart
            $body 	   = file_get_contents('php://input');
            $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

            $chanelSecret = $this->config->getChanelSecret();

            // log body and signature
            //@codingStandardsIgnoreStart
            file_put_contents('php://stderr', 'Body: ' . $body);

            // is LINE_SIGNATURE exists in request header?
            if (empty($signature)) {
                return $response->withStatus(400, 'Signature not set');
            }

            // is this request comes from LINE?
            if (\Isobar\LineBot\Constant\Config::PASS_SIGNATURE == false && ! SignatureValidator::validateSignature($body, $chanelSecret, $signature)) {
                return $response->withStatus(400, 'Invalid signature');
            }

            // init bot
            $data = json_decode($body, true);
            foreach ($data['events'] as $event) {
                $userMessage = $event['message']['text'];
                if (strtolower($userMessage) == 'hello') {
                    $message = "How can we help with our Shop? \n Our team is not available at the moment, we will get back to you soon.";
                    $result = $this->messages->replayMessage($event['replyToken'], $message);
                    return $result;
                }
            }
        });
    }
}
