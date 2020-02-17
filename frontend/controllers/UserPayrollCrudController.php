<?php

namespace frontend\controllers;

use sales\services\user\payroll\UserPayrollService;
use Yii;
use sales\model\user\entity\payroll\UserPayroll;
use sales\model\user\entity\payroll\search\UserPayrollSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserPayrollCrudController implements the CRUD actions for UserPayroll model.
 *
 * @property UserPayrollService $userPayrollService
 */
class UserPayrollCrudController extends FController
{
	/**
	 * @var UserPayrollService
	 */
	private $userPayrollService;

	public function __construct($id, $module, UserPayrollService $userPayrollService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->userPayrollService = $userPayrollService;
	}

	/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
		$behaviors =  [
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
     * Lists all UserPayroll models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserPayrollSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserPayroll model.
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
     * Creates a new UserPayroll model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserPayroll();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ups_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserPayroll model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ups_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserPayroll model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	/**
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionCalculateUserPayroll(): \yii\web\Response
	{
		if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
			$date = Yii::$app->request->post('date');
			$userId = Yii::$app->request->post('userId');
			$action = Yii::$app->request->post('action');

			$result = [
				'error' => false,
				'message' => 'Successfully calculated'
			];

			try {
				if (empty($date)) {
					throw new \RuntimeException('Date is not provided');
				}

				if ((int)$action === 1) {
					$this->userPayrollService->calcUserPayrollByYearMonth($date, $userId ?: null);
				} else if ((int)$action === 2) {
					$this->userPayrollService->recalculateUserPayroll($date, $userId ?: null);
				} else {
					throw new \RuntimeException('Unknown Action');
				}
			} catch (\RuntimeException $e) {
				$result['error'] = true;
				$result['message'] = $e->getMessage();
			} catch (\Throwable $e) {
				$result['error'] = true;
				$result['message'] = 'Internal Server Error';
				Yii::error($e->getMessage(), 'UserPayrollCrudController::actionCalculateUserPayroll::Throwable');
			}

			return $this->asJson($result);
		}
		throw new NotFoundHttpException('Page Not found');
	}

    /**
     * Finds the UserPayroll model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserPayroll the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserPayroll::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
