<?php
namespace frontend\controllers;

//use webvimark\modules\UserManagement\models\rbacDB\Role;
//use webvimark\modules\UserManagement\models\User;
use yii\web\Controller;

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
           $timezone = ($user->userParams)?$user->userParams->up_timezone:null;
           if($timezone){
               \Yii::$app->formatter->timeZone = $timezone;
           }
       }

        /*if(User::hasRole(['qa_manager'], false))    $this->layout = '@backend/themes/gentelella/views/layouts/main_qa_manager.php';
            elseif(User::hasRole(['sales_manager'], false))    $this->layout = '@backend/themes/gentelella/views/layouts/main_sales_manager.php';
                else $this->layout = '@backend/themes/gentelella/views/layouts/main.php';*/
        return parent::beforeAction($action);
    }
    public $layout = 'main.php';

}
