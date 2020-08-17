<?php

namespace frontend\controllers;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\services\clientChatService\ClientChatService;
use Yii;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class ClientChatUserChannelCrudController
 * @package frontend\controllers
 *
 * @property ClientChatService $clientChatService
 */
class ClientChatUserChannelCrudController extends FController
{

	/**
	 * @var ClientChatService
	 */
	private ClientChatService $clientChatService;

	public function __construct($id, $module, ClientChatService $clientChatService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatService = $clientChatService;
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
        $searchModel = new ClientChatUserChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ccuc_user_id, $ccuc_channel_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ccuc_user_id, $ccuc_channel_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatUserChannel();

        try {
			if ($model->load(Yii::$app->request->post()) && $model->save()) {

				$chats = ClientChat::find()->byChannel($model->ccuc_channel_id)->byOwner(null)->all();

				foreach ($chats as $chat) {
					$this->clientChatService->sendRequestToUser($chat, $model);
				}

				return $this->redirect(['view', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id]);
			}
		} catch (\DomainException | \RuntimeException $e) {
			$model->addError('general', $e->getMessage());
		} catch (\Throwable $e) {
			$model->addError('general', $e->getMessage());
			Yii::error(AppHelper::throwableFormatter($e), 'ClientChatUserChannelCrudController::actionCreate::Throwable');
		}

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ccuc_user_id, $ccuc_channel_id)
    {
        $model = $this->findModel($ccuc_user_id, $ccuc_channel_id);
        $previousChannelId = $model->ccuc_channel_id ?? null;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

        	if ($previousChannelId !== $model->ccuc_channel_id) {
				$userAccess = ClientChatUserAccess::find()->byUserId($model->ccuc_user_id)->pending()->all();
				foreach ($userAccess as $access) {
					$access->delete();
				}
				if ($userAccess) {
					$data = ClientChatAccessMessage::deleted($model->ccuc_user_id);
					Notifications::publish('clientChatRequest', ['user_id' => $model->ccuc_user_id], ['data' => $data]);
				}
				$chats = ClientChat::find()->byChannel($model->ccuc_channel_id)->byOwner(null)->all();
				foreach ($chats as $chat) {
					$this->clientChatService->sendRequestToUser($chat, $model);
				}
			}


            return $this->redirect(['view', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ccuc_user_id, $ccuc_channel_id): Response
    {
        $model = $this->findModel($ccuc_user_id, $ccuc_channel_id);

        $userAccess = ClientChatUserAccess::find()->byUserId($model->ccuc_user_id)->pending()->all();

        foreach ($userAccess as $access) {
        	$access->delete();
		}
        $model->delete();
        if ($userAccess) {
			$data = ClientChatAccessMessage::deleted($model->ccuc_user_id);
			Notifications::publish('clientChatRequest', ['user_id' => $model->ccuc_user_id], ['data' => $data]);
		}
		return $this->redirect(['index']);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return ClientChatUserChannel
     * @throws NotFoundHttpException
     */
    protected function findModel($ccuc_user_id, $ccuc_channel_id): ClientChatUserChannel
    {
        if (($model = ClientChatUserChannel::findOne(['ccuc_user_id' => $ccuc_user_id, 'ccuc_channel_id' => $ccuc_channel_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
