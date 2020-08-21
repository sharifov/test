<?php

namespace common\components\logger;

use kivork\mattermostLogTarget\Target;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class BadRequestProcessTarget extends Target
{
    public $levels = ['error'];
    public $categories = ['yii\web\HttpException:400'];

    public function formatMessage($message): ?string
    {
        [$text] = $message;
        if ($text instanceof BadRequestHttpException) {
            if ($text->getMessage() === 'Unable to verify your data submission.') {
                $data = [
                    'SERVER' => [
                        'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? null,
                        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
                    ]
                ];
                return parent::formatMessage($message) . '
Data: ' . VarDumper::dumpAsString($data);
            }
        }
        return null;
    }

    public function getMessagePrefix($message)
    {
        $userID = isset(\Yii::$app->user) ? (\Yii::$app->user->isGuest ? '-' : \Yii::$app->user->id) : '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return "[frontend][$ip][$userID]";
    }
}
