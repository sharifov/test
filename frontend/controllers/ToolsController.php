<?php

namespace frontend\controllers;

use Yii;
use common\models\ApiLog;
use common\models\search\ApiLogSearch;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
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
                        'actions' => ['clear-cache', 'supervisor'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ]
                ],
            ],
        ];
    }


    /**
     * @return \yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionClearCache()
    {

        $successItems = [];
        $warningItems = [];

        if( Yii::$app->cache->flush()) {
            $successItems[] = 'Cache is flushed';
        } else {
            $warningItems[] = 'Cache is not flushed!';
        }

        Yii::$app->db->schema->refresh();
        $successItems[] = 'DB schema refreshed!';


        $fcDir = Yii::getAlias('@frontend/runtime/cache');
        $ccDir = Yii::getAlias('@console/runtime/cache');
        $wcDir = Yii::getAlias('@webapi/runtime/cache');

        FileHelper::removeDirectory($fcDir);
        FileHelper::removeDirectory($ccDir);
        FileHelper::removeDirectory($wcDir);

        if(!file_exists($fcDir)) {
            $successItems[] = 'Removed dir '.$fcDir;
        } else {
            $warningItems[] = 'Not Removed dir '.$fcDir;
        }

        if(!file_exists($ccDir)) {
            $successItems[] = 'Removed dir ' . $ccDir;
        } else {
            $warningItems[] = 'Not Removed dir ' . $ccDir;
        }

        if(!file_exists($wcDir)) {
            $successItems[] = 'Removed dir ' . $wcDir;
        } else {
            $warningItems[] = 'Not Removed dir ' . $wcDir;
        }

        if($successItems) {
            Yii::$app->session->setFlash('success', implode('<br>', $successItems));
        }

        if($warningItems) {
            Yii::$app->session->setFlash('warning', implode('<br>', $warningItems));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSupervisor(): string
    {

        //$supervisor = new \Supervisor\Api('127.0.0.1', 9001, 'supervisor', 'Supervisor2019!');

        //$processes = $supervisor->getAllProcessInfo();
        /*foreach ($processes as $processInfo) {
            print_r($processInfo);
        }*/

        // Call Supervisor API
        //VarDumper::dump($supervisor->getAllProcessInfo(), 10, true);
         //exit;

        return $this->render('supervisor');
    }
}
