<?php

namespace frontend\controllers;

use common\models\VisitorLog;
use sales\auth\Auth;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatFeedback\entity\ClientChatFeedbackSearch;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatMessage\entity\search\ClientChatMessageSearch;
use sales\model\clientChatNote\entity\ClientChatNoteSearch;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\entity\search\ClientChatRequestSearch;
use sales\repositories\NotFoundException;
use Yii;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatQaSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use function Amp\Promise\timeoutWithDefault;

/**
 * ClientChatQaController implements the CRUD actions for ClientChat model.
 *
 * @property ClientChatRepository $clientChatRepository
 *
 */
class ClientChatQaController extends FController
{
    private ClientChatRepository $clientChatRepository;

    /**
     * ClientChatQaController constructor.
     * @param $id
     * @param $module
     * @param ClientChatRepository $clientChatRepository
     * @param array $config
     */
    public function __construct(
		$id,
		$module,
		ClientChatRepository $clientChatRepository,
		$config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatRepository = $clientChatRepository;
	}

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'room',
                    'view',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all ClientChat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientChatQaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
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
        $clientChat = ClientChat::find()
            ->byId($id)
            ->byUserGroupsRestriction()
            ->byProjectRestriction()
            ->byDepartmentRestriction()
            ->one();

        if (!$clientChat) {
            throw new NotFoundHttpException('Client chat not found or access denied');
        }

        if (!Auth::can('client-chat/view', ['chat' => $clientChat])) {
            throw new ForbiddenHttpException('Access denied.');
        }

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

        return $this->render('view', [
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
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionRoom($id): string
    {
        $clientChat = ClientChat::find()
            ->byId($id)
            ->byUserGroupsRestriction()
            ->byProjectRestriction()
            ->byDepartmentRestriction()
            ->one();

        if (!$clientChat) {
            throw new NotFoundHttpException('Client chat not found or access denied');
        }

        if (!Auth::can('client-chat/view', ['chat' => $clientChat])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if ($clientChat->isClosed()) {
            $history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('room', [
                'clientChat' => $clientChat,
                'history' => $history ?? null,
            ]);
        } else {
            return $this->render('room', [
                'clientChat' => $clientChat,
                'history' => $history ?? null,
            ]);
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function actionMessageBodyView($id): string
    {
        $model = ClientChatMessage::findOne($id);
        return $model ? '<pre>' . VarDumper::dumpAsString($model->ccm_body, 10, true) . '</pre>' : '-';
    }

    /**
     * Finds the ClientChat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClientChat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientChat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
