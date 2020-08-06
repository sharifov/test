<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\helpers\call\CallHelper;
use Yii;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\search\CallLogSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class CallLogController extends FController
{
    public function behaviors()
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
        $searchModel = new CallLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	/**
	 * @param $id
	 * @param string $breadcrumbsPreviousPage
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($id, $breadcrumbsPreviousPage = 'list'): string
    {
    	$breadcrumbsPreviousLabel = $breadcrumbsPreviousPage === 'index' ? 'Call Logs' : 'My Call Logs';
        return $this->render('view', [
            'model' => $this->findModel($id),
			'breadcrumbsPreviousPage' => $breadcrumbsPreviousPage,
			'breadcrumbsPreviousLabel' => $breadcrumbsPreviousLabel
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CallLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

	public function actionAjaxGetCallHistory()
	{
		if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

			$callSearch = new CallLogSearch();
			$page = Yii::$app->request->post('page', 0);

			$callHistory = $callSearch->getCallHistory(Auth::id());
			$callHistory->pagination->setPage($page);

			$rows = $callHistory->getModels();

            $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';

			$result = [
				'html'  => $this->renderAjax('partial/_ajax_wg_call_history', [
					'callHistory' => CallHelper::formatCallHistoryByDate($rows, $userTimezone),
				]),
				'page' => $page+1,
				'rows' => empty($rows)
			];

			return $this->asJson($result);
		}

		throw new BadRequestHttpException();
	}

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionList()
    {
        $searchModel = new CallLogSearch();
        $searchModel->createTimeRange = null;

        $dataProvider = $searchModel->searchMyCalls(Yii::$app->request->queryParams, Auth::user());

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return CallLog
     * @throws NotFoundHttpException
     */
    protected function findModel($id): CallLog
    {
        if (($model = CallLog::findOne(['cl_id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
