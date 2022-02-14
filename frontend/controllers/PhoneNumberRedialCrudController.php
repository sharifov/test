<?php

namespace frontend\controllers;

use Yii;
use src\model\phoneNumberRedial\entity\PhoneNumberRedial;
use src\model\phoneNumberRedial\entity\PhoneNumberRedialSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class PhoneNumberRedialCrudController extends FController
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
        $searchModel = new PhoneNumberRedialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $pnr_id ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($pnr_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pnr_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new PhoneNumberRedial();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'pnr_id' => $model->pnr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $pnr_id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pnr_id)
    {
        $model = $this->findModel($pnr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pnr_id' => $model->pnr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $pnr_id ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pnr_id): Response
    {
        $this->findModel($pnr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $pnr_id ID
     * @return PhoneNumberRedial
     * @throws NotFoundHttpException
     */
    protected function findModel($pnr_id): PhoneNumberRedial
    {
        if (($model = PhoneNumberRedial::findOne(['pnr_id' => $pnr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
