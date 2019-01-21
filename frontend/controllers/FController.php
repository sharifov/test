<?php
namespace frontend\controllers;

use common\models\UserConnection;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotAcceptableHttpException;

/**
 * FrontendEnd parent controller
 */
class FController extends Controller
{
    /**
     * @inheritdoc
     */
    /*public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }*/

    public function beforeAction($action)
    {
       $this->layout = '@frontend/themes/gentelella/views/layouts/main.php';

       if(!\Yii::$app->user->isGuest){
           $user = \Yii::$app->user->identity;
           $timezone = $user->userParams ? $user->userParams->up_timezone : null;
           if($timezone){
               \Yii::$app->formatter->timeZone = $timezone;
           }


           if(isset(\Yii::$app->params['limitUserConnections']) && \Yii::$app->params['limitUserConnections'] > 0) {
               $countConnections = UserConnection::find()->where(['uc_user_id' => \Yii::$app->user->id])->count();
               if ($countConnections > \Yii::$app->params['limitUserConnections'] && 'site/error' != \Yii::$app->controller->action->uniqueId) {
                   throw new NotAcceptableHttpException('Denied Access: You have too many connections (' . $countConnections . '). Close the old browser tabs and try again!');
               }
           }
       }

        /*if(User::hasRole(['qa_manager'], false))    $this->layout = '@backend/themes/gentelella/views/layouts/main_qa_manager.php';
            elseif(User::hasRole(['sales_manager'], false))    $this->layout = '@backend/themes/gentelella/views/layouts/main_sales_manager.php';
                else $this->layout = '@backend/themes/gentelella/views/layouts/main.php';*/
        return parent::beforeAction($action);
    }
    public $layout = 'main.php';

}
