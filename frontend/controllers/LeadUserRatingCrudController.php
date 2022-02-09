<?php

namespace frontend\controllers;

use Yii;
use src\model\leadUserRating\entity\LeadUserRating;
use src\model\leadUserRating\entity\LeadUserRatingSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class LeadUserRatingCrudController extends FController
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
        $searchModel = new LeadUserRatingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $lur_lead_id Lead
     * @param int $lur_user_id User
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($lur_lead_id, $lur_user_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lur_lead_id, $lur_user_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadUserRating();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lur_lead_id' => $model->lur_lead_id, 'lur_user_id' => $model->lur_user_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lur_lead_id Lead
     * @param int $lur_user_id User
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lur_lead_id, $lur_user_id)
    {
        $model = $this->findModel($lur_lead_id, $lur_user_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lur_lead_id' => $model->lur_lead_id, 'lur_user_id' => $model->lur_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lur_lead_id Lead
     * @param int $lur_user_id User
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($lur_lead_id, $lur_user_id): Response
    {
        $this->findModel($lur_lead_id, $lur_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $lur_lead_id Lead
     * @param int $lur_user_id User
     * @return LeadUserRating
     * @throws NotFoundHttpException
     */
    protected function findModel($lur_lead_id, $lur_user_id): LeadUserRating
    {
        if (($model = LeadUserRating::findOne(['lur_lead_id' => $lur_lead_id, 'lur_user_id' => $lur_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
