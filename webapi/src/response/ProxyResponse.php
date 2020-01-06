<?php

namespace webapi\src\response;

use webapi\src\response\messages\SourceMessage;
use webapi\src\response\messages\Sources;
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

    public function __construct(\yii\httpclient\Response $response)
    {
        $data = $this->processResponseData($response);

        $messages = [];

        if (!$response->isOk) {
            $messages[] = new SourceMessage(Sources::BO, $this->getStatusCodeFromResponse($response));
            $messages[] = new StatusFailedMessage();
        }

        $messages[] = $this->processStatusCodeMessage($response);

        $messages = $this->processMessages($data, $messages);

        parent::__construct(...$messages);
    }

    public function getResponse(): array
    {
        return $this->getResponseMessages();
    }

    public function getStatusCodeDefault(): int
    {
        return self::STATUS_CODE_DEFAULT;
    }

    private function processMessages($data, $messages): array
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

    private function processResponseData(\yii\httpclient\Response $response)
    {
        try {
            return $response->getData();
        } catch (\Throwable $e) {
            \Yii::error('Cant parse data. ' . $e->getMessage(), 'ProxyResponse');
            return [];
        }
    }

    private function processStatusCodeMessage(\yii\httpclient\Response $response): StatusCodeMessage
    {
        return new StatusCodeMessage($this->getStatusCodeFromResponse($response));
    }

    private function getStatusCodeFromResponse(\yii\httpclient\Response $response)
    {
        try {
            return $response->getStatusCode();
        } catch (\Throwable $e) {
            \Yii::error('Cant get status code from response. ' . $e->getMessage(), 'ProxyResponse');
            return self::STATUS_CODE_DEFAULT;
        }
    }
}
