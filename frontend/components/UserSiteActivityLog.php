<?php
namespace frontend\components;

use frontend\models\UserSiteActivity;
use \Yii;
use yii\base\Behavior;
use yii\helpers\VarDumper;
use yii\web\Application;
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
            $request_url = mb_substr(Yii::$app->request->getAbsoluteUrl(), 0, 490);

            $settings = Yii::$app->params['settings'];

            if(isset($settings['user_site_activity_time'], $settings['user_site_activity_count']) && $settings['user_site_activity_time'] && $settings['user_site_activity_count']) {

                $sec = (int) $settings['user_site_activity_time'];

                $requestCount = UserSiteActivity::find()
                    ->where(['usa_user_id' => Yii::$app->user->id, 'usa_page_url' => Yii::$app->request->pathInfo])
                    //->where(['usa_user_id' => Yii::$app->user->id, 'usa_request_url' => $request_url])
                    ->andWhere(['>=', 'usa_created_dt', date('Y-m-d H:i:s', time() - $sec)])
                    ->count();

                if($requestCount > $settings['user_site_activity_count']) {
                    // Yii::warning(VarDumper::dumpAsString(['usa_user_id' => Yii::$app->user->id, 'usa_request_url' => $request_url]), 'UserSiteActivityLog:block');
                    throw new NotAcceptableHttpException('Many requests for this url. With frequent requests, the system may block you. Please wait any time ...', 111);
                }
            }

            $activity = new UserSiteActivity();
            $activity->usa_user_id      = Yii::$app->user->id;
            $activity->usa_ip           = Yii::$app->request->getUserIP();
            $activity->usa_request_url  = $request_url;
            $activity->usa_page_url     = Yii::$app->request->pathInfo;
            $activity->usa_request_type = Yii::$app->request->isPost ? UserSiteActivity::REQUEST_TYPE_POST : UserSiteActivity::REQUEST_TYPE_GET;
            $activity->usa_request_get  = Yii::$app->request->get() ? json_encode(Yii::$app->request->get()) : null;
            // $activity->usa_request_post = Yii::$app->request->post() ? json_encode(Yii::$app->request->post()) : null;
            if(!$activity->save()) {
                Yii::error(VarDumper::dumpAsString($activity->errors), 'UserSiteActivityLog:UserSiteActivity:save');
            }

        }
    }
}