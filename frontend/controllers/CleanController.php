<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CleanController implements the CRUD actions for ApiLog model.
 */
class CleanController extends FController
{

    public $assetPaths = [
        '@frontend/web/assets',
        '@webapi/web/assets'
    ];

    public $runtimePaths = [
        '@frontend/runtime',
        '@console/runtime',
        '@webapi/runtime'
    ];
    public $caches = ['cache'];

    /**
     * @return Response
     */
    public function actionIndex() : string
    {
        return $this->render('index');
    }

    /**
     * @return Response
     * @throws \yii\base\ErrorException
     */
    public function actionAssets() : Response
    {
        foreach ((array)$this->assetPaths as $path) {
            $this->cleanDir($path);
            Yii::$app->session->addFlash('cleaner','Assets path "'	. $path .	'" is cleaned.');
        }
        //exit;
        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws \yii\base\ErrorException
     */
    public function actionRuntime() : Response
    {
        foreach ((array)$this->runtimePaths as $path) {
            $this->cleanDir($path);
            Yii::$app->session->addFlash('cleaner','Runtime path "'	. $path .	'" is cleaned.');
        }
        return $this->redirect(['index']);
    }

    /*public function actionCache()
    {
        foreach ((array)$this->caches as $cache) {
            Yii::$app->get($cache)->flush();
            Yii::$app->session->addFlash('cleaner','Cache "'	. $cache . '" is cleaned.');
        }
        return $this->redirect(['index']);
    }*/


    /**
     * @return \yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionCache() : Response
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

        foreach ($this->runtimePaths as $path) {
            $dir = Yii::getAlias($path) . '/cache';
            FileHelper::removeDirectory($dir);

            if(!file_exists($dir)) {
                $successItems[] = 'Removed dir '.$dir;
            } else {
                $warningItems[] = 'Not Removed dir '.$dir;
            }
        }


        if($successItems) {
            Yii::$app->session->setFlash('success', implode('<br>', $successItems));
        }

        if($warningItems) {
            Yii::$app->session->setFlash('warning', implode('<br>', $warningItems));
        }

        return $this->redirect(['index']); //Yii::$app->request->referrer
    }


    /**
     * @param $dir
     * @throws \yii\base\ErrorException
     */
    private function cleanDir($dir) : void
    {
        $iterator = new \DirectoryIterator(Yii::getAlias($dir));
        foreach($iterator as $sub) {
            if(!$sub->isDot() && $sub->isDir()) {
                FileHelper::removeDirectory($sub->getPathname());
            }
        }
    }

}
