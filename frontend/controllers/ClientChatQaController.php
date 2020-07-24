<?php

namespace frontend\controllers;

use common\models\VisitorLog;
use frontend\helpers\JsonHelper;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatMessage\entity\search\ClientChatMessageSearch;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatNote\entity\ClientChatNoteSearch;
use sales\repositories\NotFoundException;
use sales\services\clientChatMessage\ClientChatMessageService;
use Yii;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\search\ClientChatQaSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientChatQaController implements the CRUD actions for ClientChat model.
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatMessageService $clientChatMessageService
 */
class ClientChatQaController extends FController
{
    private ClientChatRepository $clientChatRepository;
    private ClientChatMessageService $clientChatMessageService;

    public function __construct(
		$id,
		$module,
		ClientChatRepository $clientChatRepository,
		ClientChatMessageService $clientChatMessageService,
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
     * Displays a single ClientChat model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
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

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderNotes' => $dataProviderNotes,
            'visitorLog' => $visitorLog ?? null,
            'clientChatVisitorData' => $clientChat->ccv->ccvCvd ?? null,
        ]);
    }

    /**
     * Updates an existing ClientChat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $rid
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRoom($rid)
    {

        $clientChat = $this->clientChatRepository->findByRid($rid);

        if ($clientChat->isClosed()) {
            $history = ClientChatMessage::find()->byChhId($clientChat->cch_id)->all();
        }

		return $this->render('room', [
            'clientChat' => $clientChat,
            'history' => $history ?? null,
        ]);
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
