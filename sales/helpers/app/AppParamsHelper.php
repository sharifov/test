<?php

namespace sales\helpers\app;

class AppParamsHelper
{
    public static function getVoiceMailAlias()
    {
        return \Yii::$app->params['user_voice_mail_alias'] ?? '@frontend/web/';
    }

    public static function liveChatRealTimeVisitorsUrl(): string
    {
        return \Yii::$app->params['liveChatRealTimeVisitors'] ?? '';
    }

    public static function getClientChatProjectConfigEndpoint(): string
    {
        return \Yii::$app->params['clientChat']['projectConfig']['params']['endpoint'] ?? '';
    }

    public static function isCentrifugoEnabled(): bool
    {
        return (bool)(\Yii::$app->params['centrifugo']['enabled'] ?? false);
    }

    public static function getCentrifugoWsConnectionUrl(): string
    {
        return (string) (\Yii::$app->params['centrifugo']['wsConnectionUrl'] ?? '');
    }
}
