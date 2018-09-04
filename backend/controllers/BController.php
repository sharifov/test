<?php
namespace backend\controllers;

//use webvimark\modules\UserManagement\models\rbacDB\Role;
//use webvimark\modules\UserManagement\models\User;
use yii\web\Controller;

/**
 * BackEnd parent controller
 */
class BController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->layout = '@backend/themes/gentelella/views/layouts/main.php';
        /*if(User::hasRole(['qa_manager'], false))    $this->layout = '@backend/themes/gentelella/views/layouts/main_qa_manager.php';
            elseif(User::hasRole(['sales_manager'], false))    $this->layout = '@backend/themes/gentelella/views/layouts/main_sales_manager.php';
                else $this->layout = '@backend/themes/gentelella/views/layouts/main.php';*/
        return parent::beforeAction($action);
    }
    public $layout = 'main.php';

}
