<?php

namespace modules\abac\controllers;

use frontend\controllers\FController;
use modules\abac\src\services\AbacDocService;
use src\helpers\app\AppHelper;
use src\helpers\query\QueryHelper;
use Yii;
use modules\abac\src\entities\abacDoc\AbacDoc;
use modules\abac\src\entities\abacDoc\AbacDocSearch;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class AbacDocController
 */
class AbacDocController extends FController
{
    /**
    * @return array
    */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new AbacDocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $query = clone $dataProvider->query;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'files' => QueryHelper::getColumnsAndCnt('ad_file', $query),
            'objects' => QueryHelper::getColumnsAndCnt('ad_object', $query),
            'actions' => QueryHelper::getColumnsAndCnt('ad_action', $query),
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new AbacDoc();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ad_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ad_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionScan(): Response
    {
        $abacDocService = new AbacDocService();

        try {
            $timeStart = microtime(true);
            $data = $abacDocService->parseFiles();
            $parseTimeEnd = microtime(true);
            $parseTimeSec = number_format(round($parseTimeEnd - $timeStart, 2), 2);

            if ($data) {
                $timeInsertStart = microtime(true);
                $abacDocService->insertData($data);
                $insertTimeEnd = microtime(true);
                $insertTimeSec = number_format(round($insertTimeEnd - $timeInsertStart, 2), 2);
            }

            $message = 'Processed [' . count($data) . '] items <br />';
            $message .= 'Scan Time [' . $parseTimeSec . '] sec<br />';
            if (!empty($insertTimeSec)) {
                $message .= 'Insert Time [' . $insertTimeSec . '] sec<br />';
            }

            Yii::$app->getSession()->setFlash('success', $message);
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'AbacDocController:actionScan:Throwable');
            Yii::$app->getSession()->setFlash('warning', $throwable->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return AbacDoc
     * @throws NotFoundHttpException
     */
    protected function findModel($id): AbacDoc
    {
        if (($model = AbacDoc::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('AbacDoc not found by ID(' . $id . ')');
    }
}
