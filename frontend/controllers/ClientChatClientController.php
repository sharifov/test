<?php

namespace frontend\controllers;

use sales\model\clientChat\entity\search\ClientChatClientChatsSearch;
use Yii;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ClientChatClientController extends FController
{
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'get-chats',
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionGetChats()
    {
        if (!$chatId = (int) Yii::$app->request->get('chatId')) {
            throw new BadRequestHttpException('Invalid parameter.');
        }
        if (!$chat = ClientChat::findOne($chatId)) {
            throw new NotFoundHttpException('Chat is not found.');
        }
        if (!Auth::can('client-chat/manage', ['chat' => $chat])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $chatsDataProvider = (new ClientChatClientChatsSearch())->searchChats($chat->cch_client_id);

        return $this->renderAjax('chats', [
            'client' => $chat->cchClient,
            'chatsDataProvider' => $chatsDataProvider
        ]);
    }
}
