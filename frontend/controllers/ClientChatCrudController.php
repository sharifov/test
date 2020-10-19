<?php

namespace frontend\controllers;

use common\models\Notifications;
use common\models\VisitorLog;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatQaSearch;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatFeedback\entity\ClientChatFeedbackSearch;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatMessage\entity\search\ClientChatMessageSearch;
use sales\model\clientChatNote\entity\ClientChatNoteSearch;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
        $searchModel = new ClientChatQaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('../client-chat-crud/index', [
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

        $searchModelFeedback = new ClientChatFeedbackSearch();
        $data[$searchModelFeedback->formName()]['ccf_client_chat_id'] = $id;
        $dataProviderFeedback = $searchModelFeedback->search($data);
        $dataProviderFeedback->setPagination(['pageSize' => 20]);

        return $this->render('../client-chat-crud/view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderNotes' => $dataProviderNotes,
            'visitorLog' => $visitorLog ?? null,
            'clientChatVisitorData' => $clientChat->ccv->ccvCvd ?? null,
            'dataProviderRequest' => $dataProviderRequest,
            'dataProviderFeedback' => $dataProviderFeedback,
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
        $model->cch_updated_user_id = Auth::id();
        $oldStatus = $model->cch_status_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ((ClientChat::STATUS_IDLE !== $oldStatus) && $model->isIdle()) { /* TODO:: FOR TEST  */
                Notifications::pub(
                    ['chat-' . $model->cch_id],
                    'reloadChatInfo',
                    ['data' => ClientChatAccessMessage::chatIdle($model->cch_id)]
                );
                Notifications::pub(
                    [ClientChatChannel::getPubSubKey($model->cch_channel_id)],
                    'reloadClientChatList'
                );
            }

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
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        ClientChatMessage::removeAllMessages($id);
        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionSelectAll(): Response
    {
        if (Yii::$app->request->isAjax) {
            $result = (new ClientChatQaSearch())->searchIds(Yii::$app->request->queryParams);
            return $this->asJson($result);
        }
        throw new BadRequestHttpException();
    }

    public function actionDeleteSelected(): Response
    {
        if (!Auth::user()->isAdmin()) {
            throw new NotAcceptableHttpException('Access denied');
        }

        $items = Yii::$app->request->post('selection');

        if (Yii::$app->request->isAjax && !empty($items) && is_array($items)) {
            $result = [];
            foreach ($items as $value) {
                if ($clientChat = self::findModel($value)) {
                    try {
                        $clientChat->delete();
                        ClientChatMessage::removeAllMessages($value);
                        $result[] = $value;
                    } catch (Throwable $throwable) {
                        Yii::warning(
                            VarDumper::dumpAsString($throwable),
                            'ClientChatCrudController:actionDeleteSelected'
                        );
                    }
                }
            }
            return $this->asJson($result);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param integer $id
     * @return ClientChat
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ClientChat
    {
        if (($model = ClientChat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
