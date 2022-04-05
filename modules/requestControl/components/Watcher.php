<?php

namespace modules\requestControl\components;

use modules\requestControl\models\UserSiteActivity;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use Yii;
use yii\base\Behavior;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;
use yii\web\Application;
use yii\rbac\Role;
use modules\requestControl\RequestControlModule;
use modules\requestControl\accessCheck\conditions\RoleCondition;
use modules\requestControl\accessCheck\conditions\UsernameCondition;
use modules\requestControl\accessCheck\AdmissionPass;

/**
 * Class Watcher
 * @package modules\requestControl\components
 */
class Watcher extends Behavior
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
     *
     * @throws ForbiddenHttpException
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

            $this->initRequestControl();

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

    /**
     * Function that checks user access to current resource
     *
     * @throws ForbiddenHttpException
     */
    private function initRequestControl(): void
    {
        /** @var array $settings */
        $settings = \Yii::$app->params['settings'];

        /** @var RequestControlModule $accessControlModule */
        $accessControlModule = \Yii::$app->getModule('requestControl');

        if ($accessControlModule !== null && isset($settings['user_site_activity_time'])) {
            $username = \Yii::$app->user->identity->username;
            $roleNames = $this->getUserRoles();

            /** @var AdmissionPass $checkAccess */
            $checkAccess = (new AdmissionPass((int) $settings['user_site_activity_time']))
                ->addConditionByType(UsernameCondition::TYPE, $username)
                ->addConditionByType(RoleCondition::TYPE, $roleNames);

            if ($accessControlModule->can($checkAccess) === false) {
                throw new ForbiddenHttpException(
                    "You've made too many requests recently. Please wait and try your request again later.",
                    111
                );
            }
        }
    }

    /**
     * Making the list of role names for `AdmissionPass` class
     * @var string[] $roleNames
     *
     * @return array
     */
    private function getUserRoles(): array
    {
        return array_reduce(
            \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id),
            function ($acc, $item) {
                if ($item instanceof Role) {
                    $acc[] = $item->name;
                }
                return $acc;
            },
            []
        );
    }
}
