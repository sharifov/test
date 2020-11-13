<?php
namespace frontend\controllers;

use common\models\Employee;
use common\models\UserConnection;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii2mod\rbac\filters\AccessControl;

/**
 * FrontendEnd parent controller
 */
class FController extends Controller
{

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

       if(!\Yii::$app->user->isGuest){
           /** @var Employee $user */
           $user = \Yii::$app->user->identity;

//           $user->loadCache(new \sales\model\user\entity\UserCache($user, \Yii::$app->cache));

           $timezone = $user->userParams ? $user->userParams->up_timezone : null;
           if($timezone){
               \Yii::$app->formatter->timeZone = $timezone;
           }

           if(isset(\Yii::$app->params['limitUserConnections']) && \Yii::$app->params['limitUserConnections'] > 0) {
               $countConnections = UserConnection::find()->where(['uc_user_id' => \Yii::$app->user->id])->count();
               if ($countConnections > \Yii::$app->params['limitUserConnections'] && 'site/error' != \Yii::$app->controller->action->uniqueId) {
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
