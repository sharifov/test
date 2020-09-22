<?php

namespace frontend\controllers;

use sales\helpers\setting\SettingHelper;
use sales\services\clientChatChannel\ClientChatChannelService;
use Yii;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannel\entity\search\ClientChatChannelSearch;
use yii\helpers\ArrayHelper;
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

        if ($model->load(Yii::$app->request->post())) {
        	$transaction = Yii::$app->db->beginTransaction();
        	try {
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

		$model->ccc_settings = json_encode(ClientChatChannel::getDefaultSettingList());

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
                $model->ccc_settings = json_encode(ClientChatChannel::getDefaultSettingList());
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
}
