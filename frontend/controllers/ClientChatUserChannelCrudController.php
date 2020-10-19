<?php

namespace frontend\controllers;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\services\clientChatService\ClientChatService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use Yii;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use frontend\controllers\FController;
use yii\caching\TagDependency;
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
 * @property ClientChatUserAccessService $accessService
 */
class ClientChatUserChannelCrudController extends FController
{

    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var ClientChatUserAccessService
     */
    private ClientChatUserAccessService $accessService;

    public function __construct($id, $module, ClientChatService $clientChatService, ClientChatUserAccessService $accessService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->clientChatService = $clientChatService;
        $this->accessService = $accessService;
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
                $this->accessService->setUserAccessToAllChatsByChannelIds([$model->ccuc_channel_id], $model->ccuc_user_id);
                TagDependency::invalidate(Yii::$app->cache, ClientChatUserChannel::cacheTags($model->ccuc_user_id));

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
            if ($previousChannelId !== (int)$model->ccuc_channel_id) {
                $this->accessService->disableUserAccessToAllChatsByChannelIds([$previousChannelId], $model->ccuc_user_id);
                $this->accessService->setUserAccessToAllChatsByChannelIds([$model->ccuc_channel_id], $model->ccuc_user_id);
            }
            TagDependency::invalidate(Yii::$app->cache, ClientChatUserChannel::cacheTags($model->ccuc_user_id));

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

        $model->delete();
        $this->accessService->disableUserAccessToAllChatsByChannelIds([$ccuc_channel_id], $ccuc_user_id);
        TagDependency::invalidate(Yii::$app->cache, ClientChatUserChannel::cacheTags($ccuc_user_id));
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
