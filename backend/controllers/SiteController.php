<?php
namespace backend\controllers;

use common\controllers\DefaultController;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends DefaultController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return parent::actions();
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogout()
    {
        return parent::actionLogout();
    }

    public function actionLogin()
    {
        return parent::actionLogin();
    }

    public function actionProfile()
    {
        return parent::actionProfile();
    }
}
