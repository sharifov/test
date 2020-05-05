<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\helpers\call\CallHelper;
use Yii;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\search\CallLogSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
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
     * @return string
     * @throws NotFoundHttpException
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

			$userId = Yii::$app->request->post('uid');

			$callSearch = new CallLogSearch();
			$page = Yii::$app->request->post('page', 0);

			$params['CallLogSearch']['cl_user_id'] = $userId;
			$callHistory = $callSearch->getCallHistory($params);
			$callHistory->pagination->setPage($page);

			$rows = $callHistory->getModels();

			$result = [
				'html'  => $this->renderAjax('partial/_ajax_wg_call_history', [
					'callHistory' => CallHelper::formatCallHistoryByDate($rows),
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
