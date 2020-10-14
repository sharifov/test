<?php

namespace frontend\controllers;

use sales\helpers\setting\SettingHelper;
use sales\model\clientChatChannel\entity\ClientChatChannelDefaultSettings;
use sales\repositories\NotFoundException;
use sales\services\clientChatChannel\ClientChatChannelService;
use Yii;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannel\entity\search\ClientChatChannelSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class ClientChatChannelCrudController
 * @package frontend\controllers
 *
 * @property ClientChatChannelService $channelService
 */
class ClientChatChannelCrudController extends FController
{
	/**
	 * @var ClientChatChannelService
	 */
	private ClientChatChannelService $channelService;

	public function __construct($id, $module, ClientChatChannelService $channelService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->channelService = $channelService;
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
                    'validate-all' => ['POST'],
                    'register-all' => ['POST'],
                    'un-register-all' => ['POST'],
                    'validate' => ['POST'],
                    'register' => ['POST'],
                    'un-register' => ['POST'],
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
        $searchModel = new ClientChatChannelSearch();
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
        $model = new ClientChatChannel();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        	$transaction = Yii::$app->db->beginTransaction();
        	try {
        	    $model->registered();
				if ($model->save()) {
					$this->channelService->registerChannelInRocketChat($model->ccc_id, SettingHelper::getRcNameForRegisterChannelInRc());
					$transaction->commit();
            		return $this->redirect(['view', 'id' => $model->ccc_id]);
				}
			} catch (\Throwable $e) {
				Yii::$app->session->setFlash('error', $e->getMessage());
			}
			$transaction->rollBack();
        }

		$model->ccc_settings = json_encode(ClientChatChannelDefaultSettings::getAll());

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
            return $this->redirect(['view', 'id' => $model->ccc_id]);
        } else {
            if (!$model->ccc_settings) {
                $model->ccc_settings = json_encode(ClientChatChannelDefaultSettings::getAll());
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

    public function actionSetDefault(): Response
	{
		$channelId = Yii::$app->request->get('channel_id');

		$result = [
			'error' => false,
			'message' => ''
		];

		try {
			$channel = $this->findModel((int)$channelId);

			$otherChannels = ClientChatChannel::find()->select(['ccc_id'])->where(['ccc_project_id' => $channel->ccc_project_id, 'ccc_default' => 1])->andWhere(['<>', 'ccc_id', $channel->ccc_id])->column();
			if ($otherChannels) {
				Yii::$app->db->createCommand()->update(ClientChatChannel::tableName(),
					['ccc_default' => 0],
					['ccc_id' => $otherChannels]
				)->execute();
			}
			$channel->ccc_default = 1;
			$channel->save();

			$result['message'] = 'Channel is set by default';
		} catch (NotFoundException $e) {
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}

		return $this->asJson($result);
	}

    /**
     * @param integer $id
     * @return ClientChatChannel
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ClientChatChannel
    {
        if (($model = ClientChatChannel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionValidateAll()
    {
        $report = [];
        $channels = ClientChatChannel::find()->orderBy(['ccc_id' => SORT_ASC])->all();

        foreach ($channels as $channel) {
            $report[] = $this->validate($channel);
        }

        return $this->render('report', [
            'title' => 'Validate all',
            'report' => $report,
            'backUrl' => ['index']
        ]);
    }

    public function actionValidate($id)
    {
        if (!$id) {
            throw new BadRequestHttpException('Not found channel ID');
        }

        $channel = ClientChatChannel::find()->andWhere(['ccc_id' => $id])->one();

        if (!$channel) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $report[] = $this->validate($channel);

        return $this->render('report', [
            'title' => 'Validate',
            'report' => $report,
            'backUrl' => ['view', 'id' => $id]
        ]);
    }

    public function actionRegisterAll()
    {
        $report = [];
        $channels = ClientChatChannel::find()->orderBy(['ccc_id' => SORT_ASC])->all();

        foreach ($channels as $channel) {
           $report[] = $this->register($channel);
        }

        return $this->render('report', [
            'title' => 'Register all',
            'report' => $report,
            'backUrl' => ['index']
        ]);
    }

    public function actionRegister($id)
    {
        if (!$id) {
            throw new BadRequestHttpException('Not found channel ID');
        }

        $channel = ClientChatChannel::find()->andWhere(['ccc_id' => $id])->one();

        if (!$channel) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $report[] = $this->register($channel);

        return $this->render('report', [
            'title' => 'Register',
            'report' => $report,
            'backUrl' => ['view', 'id' => $id]
        ]);
    }

    public function actionUnRegisterAll()
    {
        $report = [];
        $channels = ClientChatChannel::find()->orderBy(['ccc_id' => SORT_ASC])->all();

        foreach ($channels as $channel) {
            $report[] = $this->unRegister($channel);
        }

        return $this->render('report', [
            'title' => 'UnRegister all',
            'report' => $report,
            'backUrl' => ['index']
        ]);
    }

    public function actionUnRegister($id)
    {
        if (!$id) {
            throw new BadRequestHttpException('Not found channel ID');
        }

        $channel = ClientChatChannel::find()->andWhere(['ccc_id' => $id])->one();

        if (!$channel) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $report[] = $this->unRegister($channel);

        return $this->render('report', [
            'title' => 'UnRegister',
            'report' => $report,
            'backUrl' => ['view', 'id' => $id]
        ]);
    }

    private function register(ClientChatChannel $channel): array
    {
        try {
            $this->channelService->registerChannelInRocketChat($channel->ccc_id, SettingHelper::getRcNameForRegisterChannelInRc());
            $message = 'Registered';
            $channel->registered();
            if (!$channel->save()) {
                Yii::error([
                    'message' => 'Register client channel chat',
                    'errors' => $channel->getErrors(),
                    'model' => $channel->getAttributes(),
                ], 'ClientChatChannelCrudController');
            }
        } catch (\Throwable $e) {
            $message = $e->getMessage();
        }
        return [
            'id' => $channel->ccc_id,
            'name' => $channel->ccc_name,
            'message' => $message,
        ];
    }

    private function validate(ClientChatChannel $channel): array
    {
        try {
            $result = $this->channelService->validateChannelInRocketChat($channel->ccc_id);
            if ($result) {
                $message = 'Registered';
                $channel->registered();
            } else {
                $message = 'Not registered';
                $channel->unRegistered();
            }
            if (!$channel->save()) {
                Yii::error([
                    'message' => 'Validate client channel chat',
                    'errors' => $channel->getErrors(),
                    'model' => $channel->getAttributes(),
                ], 'ClientChatChannelCrudController');
            }
        } catch (\Throwable $e) {
            $message = $e->getMessage();
        }
        return [
            'id' => $channel->ccc_id,
            'name' => $channel->ccc_name,
            'message' => $message,
        ];
    }

    private function unRegister(ClientChatChannel $channel): array
    {
        try {
            $this->channelService->unRegisterChannelInRocketChat($channel->ccc_id);
            $message = 'Not registered';
            $channel->unRegistered();
            if (!$channel->save()) {
                Yii::error([
                    'message' => 'UnRegistered client channel chat',
                    'errors' => $channel->getErrors(),
                    'model' => $channel->getAttributes(),
                ], 'ClientChatChannelCrudController');
            }
        } catch (\Throwable $e) {
            $message = $e->getMessage();
        }
        return [
            'id' => $channel->ccc_id,
            'name' => $channel->ccc_name,
            'message' => $message,
        ];
    }
}
