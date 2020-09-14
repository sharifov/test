<?php

namespace frontend\controllers;

use common\models\VisitorLog;
use sales\auth\Auth;
use sales\model\clientChat\entity\search\ClientChatQaSearch;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatMessage\entity\search\ClientChatMessageSearch;
use sales\model\clientChatNote\entity\ClientChatNoteSearch;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use sales\services\clientChatMessage\ClientChatMessageService;
use Yii;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ClientChatCrudController extends FController
{
    private ClientChatRepository $clientChatRepository;

    /**
     * ClientChatCrudController constructor.
     * @param $id
     * @param $module
     * @param ClientChatRepository $clientChatRepository
     * @param array $config
     */
    public function __construct($id, $module, ClientChatRepository $clientChatRepository, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatRepository = $clientChatRepository;
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ClientChatQaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../client-chat-qa/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($id): string
    {
        $clientChat = $this->clientChatRepository->findById($id);

        $searchModel = new ClientChatMessageSearch();
        $data[$searchModel->formName()]['ccm_cch_id'] = $id;
        $dataProvider = $searchModel->search($data);

        $searchModelNotes = new ClientChatNoteSearch();
        $data[$searchModelNotes->formName()]['ccn_chat_id'] = $id;
        $dataProviderNotes = $searchModelNotes->search($data);
        $dataProviderNotes->setPagination(['pageSize' => 20]);

        if ($clientChat->ccv && $clientChat->ccv->ccv_cvd_id) {
            $visitorLog = VisitorLog::find()->byCvdId($clientChat->ccv->ccv_cvd_id)->orderBy(['vl_created_dt' => SORT_DESC])->one();
        }

        $requestSearch = new ClientChatRequestSearch();
        $visitorId = '';
		if ($clientChat->ccv && $clientChat->ccv->ccvCvd) {
		    $visitorId = $clientChat->ccv->ccvCvd->cvd_visitor_rc_id ?? '';
		}
        $data[$requestSearch->formName()]['ccr_visitor_id'] = $visitorId;
        $data[$requestSearch->formName()]['ccr_event'] = ClientChatRequest::EVENT_TRACK;
        $dataProviderRequest = $requestSearch->search($data);
        $dataProviderRequest->setPagination(['pageSize' => 10]);

        return $this->render('../client-chat-qa/view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderNotes' => $dataProviderNotes,
            'visitorLog' => $visitorLog ?? null,
            'clientChatVisitorData' => $clientChat->ccv->ccvCvd ?? null,
            'dataProviderRequest' => $dataProviderRequest,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChat();

		$model->cch_created_user_id = Auth::id();
		$model->cch_updated_user_id = Auth::id();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cch_id]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->cch_updated_user_id = Auth::id();
			return $this->redirect(['view', 'id' => $model->cch_id]);
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
     * @return ClientChat
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ClientChat
    {
        if (($model = ClientChat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
