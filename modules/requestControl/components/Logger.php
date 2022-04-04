<?php

namespace modules\requestControl\components;

use modules\requestControl\models\UserSiteActivity;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use Yii;
use yii\base\Behavior;
use yii\helpers\StringHelper;
use yii\web\Application;

/**
 * Class Logger
 * @package frontend\components
 */
class Logger extends Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'activityLog',
        ];
    }

    /**
     * Function that save user activity in database
     */
    public function activityLog(): void
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
            if (strpos(Yii::$app->request->getAbsoluteUrl(), 'call/record') !== false) {
                return;
            }
            if (strpos(Yii::$app->request->getAbsoluteUrl(), 'smart/default/async-poll') !== false) {
                return;
            }
            if (strpos(Yii::$app->request->getAbsoluteUrl(), 'smart/default/async-get') !== false) {
                return;
            }

            $request_url = mb_substr(Yii::$app->request->getAbsoluteUrl(), 0, 490);

            try {
                $activity = new UserSiteActivity();
                $activity->usa_user_id      = Yii::$app->user->id;
                $activity->usa_ip           = Yii::$app->request->getUserIP();
                $activity->usa_request_url  = $request_url;
                $activity->usa_page_url     = StringHelper::truncate(Yii::$app->request->pathInfo, 250);
                $activity->usa_request_type = Yii::$app->request->isPost ? UserSiteActivity::REQUEST_TYPE_POST : UserSiteActivity::REQUEST_TYPE_GET;
                $activity->usa_request_get  = Yii::$app->request->get() ? json_encode(Yii::$app->request->get()) : null;

                if (!$activity->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($activity));
                }
                if (strlen(Yii::$app->request->pathInfo) > 255) {
                    throw new \RuntimeException('Len pathInfo over limit (255). ' . Yii::$app->request->pathInfo);
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'UserSiteActivityLog:UserSiteActivity:Exception');
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable), 'UserSiteActivityLog:UserSiteActivity:Throwable');
            }
        }
    }
}
