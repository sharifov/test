<?php

namespace frontend\controllers;

use common\controllers\DefaultController;
use Yii;
use common\models\ApiLog;
use common\models\search\ApiLogSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ToolsController implements the CRUD actions for ApiLog model.
 */
class ToolsController extends FController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['clear-cache'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ]
                ],
            ],
        ];
    }


    /**
     * Lists all ApiLog models.
     * @return mixed
     */
    public function actionClearCache()
    {
        Yii::$app->cache->flush();

        return $this->redirect(Yii::$app->request->referrer);
    }
}
