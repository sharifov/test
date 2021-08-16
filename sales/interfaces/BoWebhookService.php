<?php

namespace sales\interfaces;

use yii\base\Model;

interface BoWebhookService
{
    public function processRequest(Model $form): void;
}
