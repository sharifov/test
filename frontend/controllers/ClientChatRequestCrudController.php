<?php

namespace frontend\controllers;

use sales\model\clientChatRequest\repository\ClientChatRequestRepository;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use Yii;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class ClientChatRequestCrudController
 * @package frontend\controllers
 *
 * @property ClientChatRequestRepository $requestRepository
 */
class ClientChatRequestCrudController extends FController
{
    /**
     * @var ClientChatRequestRepository
     */
    private ClientChatRequestRepository $requestRepository;

    public function __construct($id, $module, ClientChatRequestRepository $requestRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->requestRepository = $requestRepository;
    }

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
        $searchModel = new ClientChatRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
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
        $model = new ClientChatRequest();

        if ($model->load(Yii::$app->request->post())) {
            try {
                $this->requestRepository->save($model);
                return $this->redirect(['view', 'id' => $model->ccr_id]);
            } catch (\Throwable $e) {
                $model->addError('general' , $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            try {
                $this->requestRepository->save($model);
                return $this->redirect(['view', 'id' => $model->ccr_id]);
            } catch (\Throwable $e) {
                $model->addError('general', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return ClientChatRequest
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ClientChatRequest
    {
        if (($model = ClientChatRequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
