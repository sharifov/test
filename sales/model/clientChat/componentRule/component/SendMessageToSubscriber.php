<?php

namespace sales\model\clientChat\componentRule\component;

class SendMessageToSubscriber implements RunnableComponentInterface
{
    public function run(): void
    {
        \Yii::info('send message subscriber', 'info\SendMessageToSubscriber');
    }
}
