<?php

namespace webapi\src\response;

use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\StatusFailedMessage;
use Yii;
use webapi\src\response\messages\Message;

/**
 * Class ProxyResponse
 */
class ProxyResponse extends Response
{
    public const STATUS_CODE_DEFAULT = 200;

    public function __construct(\yii\httpclient\Response $response, Message ...$messages)
    {
        $messages[] = new StatusCodeMessage(self::getStatusCodeFromResponse($response));

        $data = self::processResponseData($response);
        $messages = self::processMessages($data, $messages);

        if (!$response->isOk) {
            $messages[] = new StatusFailedMessage();
        }

        parent::__construct(...$messages);
    }

    public function getResponse(): array
    {
        $this->sortUp(Message::STATUS_MESSAGE);
        $this->sortDown(Message::SOURCE_MESSAGE);
        return $this->getResponseMessages();
    }

    public function getStatusCodeDefault(): int
    {
        return self::STATUS_CODE_DEFAULT;
    }

    private static function processMessages($data, $messages): array
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $messages[] = new Message($key, $value);
            }
        } else {
            Yii::error('Response data is not array.', 'ProxyResponse');
        }
        return $messages;
    }

    private static function processResponseData(\yii\httpclient\Response $response)
    {
        try {
            return $response->getData();
        } catch (\Throwable $e) {
            \Yii::error('Cant parse data. ' . $e->getMessage(), 'ProxyResponse');
            return [];
        }
    }

    private static function getStatusCodeFromResponse(\yii\httpclient\Response $response)
    {
        try {
            return $response->getStatusCode();
        } catch (\Throwable $e) {
            \Yii::error('Cant get status code from response. ' . $e->getMessage(), 'ProxyResponse');
            return self::STATUS_CODE_DEFAULT;
        }
    }
}
