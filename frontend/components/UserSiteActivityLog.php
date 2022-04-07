<?php

namespace frontend\components;

use frontend\models\UserSiteActivity;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\Application;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;

class UserSiteActivityLog extends Behavior
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
     *
     */
    public function activityLog(): void
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
            /*if (strpos(Yii::$app->request->getAbsoluteUrl(), 'lead/check-updates') !== false) {
                return true;
            }*/
            $absUrl = Yii::$app->request->getAbsoluteUrl();
            if (strpos($absUrl, 'call/record') !== false) {
                return;
            } else if (strpos($absUrl, 'smart/default/async-poll') !== false) {
                return;
            } else if (strpos($absUrl, 'smart/default/async-get') !== false) {
                return;
            } else if (strpos($absUrl, 'smart/default/bestdeal') !== false) {
                return;
            }

            $request_url = mb_substr($absUrl, 0, 490);

            $settings = Yii::$app->params['settings'];

            if (isset($settings['user_site_activity_time'], $settings['user_site_activity_count']) && $settings['user_site_activity_time'] && $settings['user_site_activity_count']) {
                $sec = (int) $settings['user_site_activity_time'];

                $requestCount = UserSiteActivity::find()
                    ->where(['usa_user_id' => Yii::$app->user->id, 'usa_page_url' => Yii::$app->request->pathInfo])
                    //->where(['usa_user_id' => Yii::$app->user->id, 'usa_request_url' => $request_url])
                    ->andWhere(['>=', 'usa_created_dt', date('Y-m-d H:i:s', time() - $sec)])
                    ->count();

                if ($requestCount > $settings['user_site_activity_count']) {
                    throw new ForbiddenHttpException('You\'ve made too many requests recently. Please wait and try your request again later.', 111);
                }
            }

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
