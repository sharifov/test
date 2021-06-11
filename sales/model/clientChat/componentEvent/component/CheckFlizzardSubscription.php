<?php

namespace sales\model\clientChat\componentEvent\component;

use yii\helpers\VarDumper;

class CheckFlizzardSubscription implements ComponentEventInterface
{
    public function run(ComponentDTOInterface $dto): string
    {
        \Yii::info(VarDumper::dumpAsString([
            'chatId' => $dto->getClientChatEntity()->cch_id,
            'channelId' => $dto->getChannelId(),
            'componentEventConfig' => $dto->getComponentEventConfig()
        ]), 'info\CheckFlizzardSubscription');
        return 'true';
    }
}
