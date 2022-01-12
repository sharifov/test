<?php

namespace src\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use Markdownify\Converter;
use Markdownify\ConverterExtra;
use src\model\clientChat\componentEvent\component\ComponentDTOInterface;
use src\model\clientChat\componentRule\component\defaultConfig\SendMessageDefaultConfig;
use Yii;
use yii\helpers\VarDumper;

class SendMessageToSubscriber implements RunnableComponentInterface
{
    public function run(ComponentDTOInterface $dto): void
    {
//        Yii::info('SendMessageToSubscriber isNew: ' . $dto->getIsChatNew(), 'info\SendMessageToSubscriber');
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
//            Yii::info('SendMessageToSubscriber sent message with data: ' . VarDumper::dumpAsString($data), 'info\SendMessageToSubscriber');
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
