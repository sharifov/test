<?php

namespace sales\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use Markdownify\Converter;
use Markdownify\ConverterExtra;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;
use sales\model\clientChat\componentRule\component\defaultConfig\SendMessageDefaultConfig;
use Yii;

class SendMessageToSubscriber implements RunnableComponentInterface
{
    public function run(ComponentDTOInterface $dto): void
    {
        if ($dto->getIsChatNew()) {
            $converter = new ConverterExtra(Converter::LINK_IN_PARAGRAPH);

            $config = $dto->getRunnableComponentConfig();

            $data = [
                'message' => [
                    'rid' => $dto->getClientChatEntity()->cch_rid,
                    'msg' => trim($converter->parseString($config['message'] ?? 'You do not have a subscription to chat with an agent'))
                ],
            ];
            $headers = Yii::$app->rchat->getSystemAuthDataHeader();
            $response = Yii::$app->chatBot->sendMessage($data, $headers);

            if ($response['error']) {
                Yii::warning($response['error']['error'] ?? 'Unknown error from chat bot', 'SendMessageToSubscriberComponent::run');
            }
        }
    }

    public function getDefaultConfig(): array
    {
        return SendMessageDefaultConfig::getConfig();
    }

    public function getDefaultConfigJson(): string
    {
        return JsonHelper::encode($this->getDefaultConfig());
    }
}
