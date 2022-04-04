<?php

namespace modules\requestControl\components;

use Yii;
use yii\base\Behavior;
use yii\rbac\Role;
use modules\requestControl\RequestControlModule;
use modules\requestControl\accessCheck\conditions\RoleCondition;
use modules\requestControl\accessCheck\conditions\UsernameCondition;
use modules\requestControl\accessCheck\AdmissionPass;
use yii\web\ForbiddenHttpException;

/**
 * Class Watcher
 * @package modules\requestControl\components
 */
class Watcher extends Behavior
{
    /**
     * Function that checks user access to current resource
     *
     * @throws ForbiddenHttpException
     */
    public function checkAccess(): void
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
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

                if ($accessControlModule->can($checkAccess) === false)
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
            function($acc, $item) {
                if ($item instanceof Role)
                    $acc[] = $item->name;
                return $acc;
            },
            []
        );
    }
}