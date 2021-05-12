<?php

namespace frontend\controllers;

use Yii;
use sales\model\project\entity\projectRelation\ProjectRelation;
use sales\model\project\entity\projectRelation\search\ProjectRelationSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class ProjectRelationCrudController
 */
class ProjectRelationCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

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

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ProjectRelationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $prl_project_id
     * @param int $prl_related_project_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($prl_project_id, $prl_related_project_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($prl_project_id, $prl_related_project_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProjectRelation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'prl_project_id' => $model->prl_project_id, 'prl_related_project_id' => $model->prl_related_project_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $prl_project_id
     * @param int $prl_related_project_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($prl_project_id, $prl_related_project_id)
    {
        $model = $this->findModel($prl_project_id, $prl_related_project_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'prl_project_id' => $model->prl_project_id, 'prl_related_project_id' => $model->prl_related_project_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $prl_project_id
     * @param integer $prl_related_project_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($prl_project_id, $prl_related_project_id): Response
    {
        $this->findModel($prl_project_id, $prl_related_project_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $prl_project_id
     * @param integer $prl_related_project_id
     * @return ProjectRelation
     * @throws NotFoundHttpException
     */
    protected function findModel($prl_project_id, $prl_related_project_id): ProjectRelation
    {
        if (($model = ProjectRelation::findOne(['prl_project_id' => $prl_project_id, 'prl_related_project_id' => $prl_related_project_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('ProjectRelation not found');
    }
}
