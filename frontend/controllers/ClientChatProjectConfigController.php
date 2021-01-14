<?php

namespace frontend\controllers;

use frontend\helpers\JsonHelper;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfigDefaultParams;
use sales\model\clientChat\entity\projectConfig\service\ClientChatProjectConfigService;
use Yii;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use sales\model\clientChat\entity\projectConfig\search\ClientChatProjectConfigSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientChatProjectConfigController implements the CRUD actions for ClientChatProjectConfig model.
 *
 * @property ClientChatProjectConfigService $chatProjectConfigService
 */
class ClientChatProjectConfigController extends FController
{
    /**
     * @var ClientChatProjectConfigService
     */
    private ClientChatProjectConfigService $chatProjectConfigService;

    public function __construct($id, $module, ClientChatProjectConfigService $chatProjectConfigService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->chatProjectConfigService = $chatProjectConfigService;
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

    /**
     * Lists all ClientChatProjectConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientChatProjectConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientChatProjectConfig model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ClientChatProjectConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClientChatProjectConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->chatProjectConfigService->deleteConfigCacheActiveLanguages($model->ccpc_project_id);
            return $this->redirect(['view', 'id' => $model->ccpc_project_id]);
        }

        $model->ccpc_params_json = JsonHelper::encode(ClientChatProjectConfigDefaultParams::getParams());
        $model->ccpc_theme_json = JsonHelper::encode(ClientChatProjectConfigDefaultParams::getTheme());
        $model->ccpc_registration_json = JsonHelper::encode(ClientChatProjectConfigDefaultParams::getRegistration());
        $model->ccpc_settings_json = JsonHelper::encode(ClientChatProjectConfigDefaultParams::getSettings());

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClientChatProjectConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->chatProjectConfigService->deleteConfigCacheActiveLanguages($model->ccpc_project_id);
            return $this->redirect(['view', 'id' => $model->ccpc_project_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClientChatProjectConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $projectId = $model->ccpc_project_id;
        if ($model->delete()) {
            $this->chatProjectConfigService->deleteConfigCacheActiveLanguages($projectId);
        }
        return $this->redirect(['index']);
    }

    public function actionDeleteCache(): \yii\web\Response
    {
        $projectId = (int)Yii::$app->request->get('projectId');
        if (!$projectId) {
            throw new BadRequestHttpException('Invalid project id');
        }

        $model = $this->findModel($projectId);

        if ($keyCacheNotDeleted = $this->chatProjectConfigService->deleteConfigCacheActiveLanguages($model->ccpc_project_id)) {
            Yii::$app->session->setFlash('warning', 'Cache was not deleted because cache with keys is not exists: ' . implode(', ', $keyCacheNotDeleted));
        } else {
            Yii::$app->session->setFlash('success', 'Cache was deleted successfully');
        }
        return $this->redirect(['view', 'id' => $projectId]);
    }

    /**
     * Finds the ClientChatProjectConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClientChatProjectConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClientChatProjectConfig::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
