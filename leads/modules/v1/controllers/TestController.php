<?php

declare(strict_types=1);

namespace leads\modules\v1\controllers;

use yii\rest\Controller;

class TestController extends Controller
{
    public function actionTest(): array
    {
        return [
            'action' => 'test'
        ];
    }
}
