<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\UserConnection;
use sales\helpers\setting\SettingHelper;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii2mod\rbac\filters\AccessControl;

/**
 * FrontendEnd parent controller
 */
class FController extends Controller
{

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function layoutCrud(): void
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_crud.php';
    }

    public function beforeAction($action)
    {
//       $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main.php';

        if (!\Yii::$app->user->isGuest && !\Yii::$app->request->isAjax) {
            /** @var Employee $user */
            $user = \Yii::$app->user->identity;

            $timezone = $user->userParams ? $user->userParams->up_timezone : null;
            if ($timezone) {
                \Yii::$app->formatter->timeZone = $timezone;
            }

            unset($user, $timezone);

            $limitUserConnection = SettingHelper::getLimitUserConnection() ?: \Yii::$app->params['limitUserConnections'];

            if ($limitUserConnection > 0) {
                $countConnections = UserConnection::find()->where(['uc_user_id' => \Yii::$app->user->id])->count();
                if ($countConnections >= $limitUserConnection && 'site/error' != \Yii::$app->controller->action->uniqueId) {
                    throw new ForbiddenHttpException('Denied Access: You have too many connections (' . $countConnections . '). Close the old browser tabs and try again!');
                }
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * @param array $errors
     * @return string
     */
    public function getParsedErrors(array $errors): string
    {
        return implode('<br>', array_map(static function ($errors) {
            return implode('<br>', $errors);
        }, $errors));
    }
}
