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
}