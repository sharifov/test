<?php

namespace frontend\controllers;

use Yii;
use sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup;
use sales\model\phoneLine\phoneLineUserGroup\entity\search\PhoneLineUserGroupSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class PhoneLineUserGroupCrudController extends FController
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new PhoneLineUserGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $plug_line_id
     * @param integer $plug_ug_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($plug_line_id, $plug_ug_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($plug_line_id, $plug_ug_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new PhoneLineUserGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'plug_line_id' => $model->plug_line_id, 'plug_ug_id' => $model->plug_ug_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $plug_line_id
     * @param integer $plug_ug_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($plug_line_id, $plug_ug_id)
    {
        $model = $this->findModel($plug_line_id, $plug_ug_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'plug_line_id' => $model->plug_line_id, 'plug_ug_id' => $model->plug_ug_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $plug_line_id
     * @param integer $plug_ug_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($plug_line_id, $plug_ug_id): Response
    {
        $this->findModel($plug_line_id, $plug_ug_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $plug_line_id
     * @param integer $plug_ug_id
     * @return PhoneLineUserGroup
     * @throws NotFoundHttpException
     */
    protected function findModel($plug_line_id, $plug_ug_id): PhoneLineUserGroup
    {
        if (($model = PhoneLineUserGroup::findOne(['plug_line_id' => $plug_line_id, 'plug_ug_id' => $plug_ug_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
