<?php

namespace frontend\components;

use modules\requestControl\models\UserSiteActivity;
use modules\requestControl\accessCheck\conditions\RoleCondition;
use modules\requestControl\accessCheck\conditions\UsernameCondition;
use modules\requestControl\accessCheck\AdmissionPass;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use Yii;
use yii\base\Behavior;
use yii\helpers\StringHelper;
use yii\rbac\Role;
use yii\web\Application;
use yii\web\ForbiddenHttpException;

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
     * @throws ForbiddenHttpException
     */
    public function activityLog(): void
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
            /*if (strpos(Yii::$app->request->getAbsoluteUrl(), 'lead/check-updates') !== false) {
                return true;
            }*/
            if (strpos(Yii::$app->request->getAbsoluteUrl(), 'voip/log') !== false) {
                return;
            }
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

            $settings = Yii::$app->params['settings'];

            /*
             * Checking and filtering for frequent page refreshes
             *
             * NOTICE [UserSiteActivityLog_A_001]: this business logic may looks better in specific apart filter. This class (`UserSiteActivityLog`) judging by the name - have to do logging and nothing more.
             *
             * Mixing the logging and access control logic in one class (especially at one function) very hard violation of `Single Responsibility Principle`.
             *
             * [UserSiteActivityLog_A_001] BEGIN >>>
             */

            $accessControlModule = \Yii::$app->getModule('requestControl');

            if ($accessControlModule !== null && isset($settings['user_site_activity_time'])) {
                /**
                 * Making the list of role names for `AdmissionPass` class
                 * @var string[] $roleNames
                 */
                $roleNames = array_reduce(
                    \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id),
                    function ($acc, $item) {
                        if ($item instanceof Role) {
                            $acc[] = $item->name;
                        }
                        return $acc;
                    },
                    []
                );

                // Access checking
                $admissionPass = new AdmissionPass(
                    (int)\Yii::$app->user->id,
                    (string)\Yii::$app->request->pathInfo,
                    (int)$settings['user_site_activity_time']
                );
                $checkAccess = ($admissionPass)
                    ->addConditionByType(UsernameCondition::TYPE, \Yii::$app->user->identity->username)
                    ->addConditionByType(RoleCondition::TYPE, $roleNames);

                if ($accessControlModule->can($checkAccess) === false) {
                    throw new ForbiddenHttpException(
                        "You've made too many requests recently. Please wait and try your request again later.",
                        111
                    );
                }
            }

            // <<< [UserSiteActivityLog_A_001] END

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
