<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\services\cleaner\DbCleanerService;
use sales\services\cleaner\form\DbCleanerParamsForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
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

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return Response
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * @return Response
     * @throws \yii\base\ErrorException
     */
    public function actionAssets(): Response
    {
        foreach ((array)$this->assetPaths as $path) {
            $this->cleanDir($path);
            Yii::$app->session->addFlash('cleaner', 'Assets path "' . $path .   '" is cleaned.');
        }
        //exit;
        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws \yii\base\ErrorException
     */
    public function actionRuntime(): Response
    {
        foreach ((array)$this->runtimePaths as $path) {
            $this->cleanDir($path);
            Yii::$app->session->addFlash('cleaner', 'Runtime path "'    . $path .   '" is cleaned.');
        }
        return $this->redirect(['index']);
    }

    /*public function actionCache()
    {
        foreach ((array)$this->caches as $cache) {
            Yii::$app->get($cache)->flush();
            Yii::$app->session->addFlash('cleaner','Cache "'    . $cache . '" is cleaned.');
        }
        return $this->redirect(['index']);
    }*/


    /**
     * @return \yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionCache(): Response
    {
        $successItems = [];
        $warningItems = [];

        if (Yii::$app->cache->flush()) {
            $successItems[] = 'Cache is flushed';
        } else {
            $warningItems[] = 'Cache is not flushed!';
        }

        if (Yii::$app->cacheFile->flush()) {
            $successItems[] = 'File Cache is flushed';
        } else {
            $warningItems[] = 'File Cache is not flushed!';
        }

        Yii::$app->db->schema->refresh();
        $successItems[] = 'DB schema refreshed!';

        foreach ($this->runtimePaths as $path) {
            $dir = Yii::getAlias($path) . '/cache';
            FileHelper::removeDirectory($dir);

            if (!file_exists($dir)) {
                $successItems[] = 'Removed dir ' . $dir;
            } else {
                $warningItems[] = 'Not Removed dir ' . $dir;
            }
        }

        if ($successItems) {
            Yii::$app->session->setFlash('success', implode('<br>', $successItems));
        }

        if ($warningItems) {
            Yii::$app->session->setFlash('warning', implode('<br>', $warningItems));
        }

        return $this->redirect(['index']); //Yii::$app->request->referrer
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCleanTableAjax(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = ['message' => '', 'status' => 0];

            try {
                if (!Auth::can('global/clean/table')) {
                    throw new ForbiddenHttpException('You don\'t have access to this page', -1);
                }
                $form = new DbCleanerParamsForm();
                if (!$form->load(Yii::$app->request->post())) {
                    throw new BadRequestHttpException('Form not loaded from post request', -2);
                }
                if (!$form->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($form), -3);
                }

                $dbCleanerService = new DbCleanerService();
                $cleaner = $dbCleanerService->initClass($form->table);
                $processed = $cleaner->runDeleteByForm($form);

                if ($processed) {
                    $message = 'Processed ' . $processed . ' records';
                } else {
                    $message = 'No records found matching the specified criteria';
                }

                $result['message'] = $message;
                $result['status'] = 1;
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'CleanController:actionCleanTableAjax:throwable'
                );
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }


    /**
     * @param $dir
     * @throws \yii\base\ErrorException
     */
    private function cleanDir($dir): void
    {
        $iterator = new \DirectoryIterator(Yii::getAlias($dir));
        foreach ($iterator as $sub) {
            if (!$sub->isDot() && $sub->isDir()) {
                FileHelper::removeDirectory($sub->getPathname());
            }
        }
    }
}
