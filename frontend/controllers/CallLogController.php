<?php

namespace frontend\controllers;

use common\models\Employee;
use src\auth\Auth;
use src\helpers\call\CallHelper;
use src\model\call\abac\dto\CallLogObjectAbacDto;
use Yii;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\search\CallLogSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use src\model\call\abac\CallAbacObject;

/**
 * Class CallLogController
 * @package frontend\controllers
 */
class CallLogController extends FController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'only' => [
                    'index',
                    'create'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws \Exception
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
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($id, $breadcrumbsPreviousPage = 'list'): string
    {
        $model = $this->findModel($id);
        /** @var Employee $user */
        $user = \Yii::$app->user->identity;
        $dto = new CallLogObjectAbacDto($model, $user);
        if (!\Yii::$app->abac->can($dto, CallAbacObject::OBJ_CALL_LOG, CallAbacObject::ACTION_VIEW, $user)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $breadcrumbsPreviousLabel = $breadcrumbsPreviousPage === 'index' ? 'Call Logs' : 'My Call Logs';
        return $this->render('view', [
            'model' => $model,
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
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /** @var Employee $user */
        $user = \Yii::$app->user->identity;
        $dto = new CallLogObjectAbacDto($model, $user);
        /** @abac $callLogAbacDto, CallLogAbacObject::OBJ_CALL_LOG, CallLogAbacObject::ACTION_UPDATE, Call log act update */
        if (!Yii::$app->abac->can($dto, CallAbacObject::OBJ_CALL_LOG, CallAbacObject::ACTION_UPDATE, $user)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxGetCallHistory()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $callSearch = new CallLogSearch();
            $page = (int)Yii::$app->request->post('page', 0);

            $callHistory = $callSearch->getCallHistory(Auth::id());
            $callHistory->pagination->setPage($page);

            $rows = $callHistory->getModels();

            $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';

            $result = [
                'html'  => $this->renderAjax('partial/_ajax_wg_call_history', [
                    'callHistory' => CallHelper::formatCallHistoryByDate($rows, $userTimezone),
                    'page' => ($page + 1),
                    'userId' => Auth::id()
                ]),
                'page' => $page + 1,
                'rows' => empty($rows)
            ];

            return $this->asJson($result);
        }

        throw new BadRequestHttpException();
    }

    /**
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $model = $this->findModel($id);
        /** @var Employee $user */
        $user = \Yii::$app->user->identity;
        $dto = new CallLogObjectAbacDto($model, $user);
        /** @abac $callLogAbacDto, CallLogAbacObject::OBJ_CALL_LOG, CallLogAbacObject::ACTION_UPDATE, Call log act update */
        if (!Yii::$app->abac->can($dto, CallAbacObject::OBJ_CALL_LOG, CallAbacObject::ACTION_DELETE, $user)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function actionList()
    {
        $searchModel = new CallLogSearch();
        $searchModel->createTimeRange = null;

        $dataProvider = $searchModel->searchMyCalls(Yii::$app->request->queryParams, Auth::user()->id);

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
