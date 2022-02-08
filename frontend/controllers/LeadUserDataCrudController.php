<?php

namespace frontend\controllers;

use Yii;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class LeadUserDataCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new LeadUserDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $lud_id ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($lud_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lud_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadUserData();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lud_id' => $model->lud_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lud_id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lud_id)
    {
        $model = $this->findModel($lud_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lud_id' => $model->lud_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lud_id ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($lud_id): Response
    {
        $this->findModel($lud_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $lud_id ID
     * @return LeadUserData
     * @throws NotFoundHttpException
     */
    protected function findModel($lud_id): LeadUserData
    {
        if (($model = LeadUserData::findOne(['lud_id' => $lud_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('LeadUserData not found by ID(' . $lud_id . ')');
    }
}
