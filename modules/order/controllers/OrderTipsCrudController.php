<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\services\OrderTipsManageService;
use Yii;
use modules\order\src\entities\orderTips\OrderTips;
use modules\order\src\entities\orderTips\search\OrderTipsSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderTipsCrudController implements the CRUD actions for OrderTips model.
 *
 * @property OrderTipsManageService $orderTipsManageService
 */
class OrderTipsCrudController extends FController
{

	/**
	 * @var OrderTipsManageService
	 */
	private $orderTipsManageService;

	public function __construct($id, $module, OrderTipsManageService $orderTipsManageService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->orderTipsManageService = $orderTipsManageService;
	}

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
     * Lists all OrderTips models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderTipsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderTips model.
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
     * Creates a new OrderTips model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderTips();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

			try {
				$this->orderTipsManageService->create($model);

            	return $this->redirect(['view', 'id' => $model->ot_order_id]);
			} catch (\RuntimeException $e) {
				$model->addError('runtimeError', $e->getMessage());
			} catch (\Throwable $e) {
				Yii::error($e->getMessage(), 'OrderTipsCrudController::actionCreate::Throwable');
				$model->addError('internalServerError', 'Internal Server Error');
			}

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderTips model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

        	try {
        		$this->orderTipsManageService->update($model);

            	return $this->redirect(['view', 'id' => $model->ot_order_id]);
			} catch (\RuntimeException $e) {
				$model->addError('runtimeError', $e->getMessage());
			} catch (\Throwable $e) {
				Yii::error($e->getMessage(), 'OrderTipsCrudController::actionCreate::Throwable');
				$model->addError('internalServerError', 'Internal Server Error');
			}

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

	/**
	 * @param $id
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderTips model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderTips the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderTips::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
