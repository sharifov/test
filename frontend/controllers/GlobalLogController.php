<?php

namespace frontend\controllers;

use Yii;
use common\models\GlobalLog;
use common\models\search\GlobalLogSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GlobalLogController implements the CRUD actions for GlobalLog model.
 */
class GlobalLogController extends FController
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

    /**
     * Lists all GlobalLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GlobalLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GlobalLog model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new GlobalLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GlobalLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->gl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

	/**
	 * @return string
	 * @throws BadRequestHttpException
	 */
	public function actionAjaxViewGeneralLeadLog(): string
	{
		if (Yii::$app->request->isAjax) {
			$leadId = Yii::$app->request->get('lid');

			$searchModel = new GlobalLogSearch();
			$params = Yii::$app->request->queryParams;
			$params['GlobalLogSearch']['leadId'] = $leadId;
			$dataProvider = $searchModel->searchByLead($params);

			return $this->renderAjax('partial/_general_lead_log', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'lid' => $leadId,
			]);
		}
		throw new BadRequestHttpException();
	}

    /**
     * Finds the GlobalLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GlobalLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GlobalLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
