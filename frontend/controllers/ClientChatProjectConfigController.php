<?php

namespace frontend\controllers;

use sales\model\clientChat\entity\projectConfig\service\ClientChatProjectConfigService;
use Yii;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use sales\model\clientChat\entity\projectConfig\search\ClientChatProjectConfigSearch;
use frontend\controllers\FController;
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
        } else {
            $model->ccpc_params_json = '{
  "endpoint": "chatbot.travel-dev.com",
  "notificationSound": "https://cdn.travelinsides.com/npmstatic/assets/chime.mp3",
  "autoMessage": {
    "enabled": false,
    "message": "",
    "delay": 1,
    "repeatDelay": 1,
    "botName": "",
    "botAvatar": ""
  },
  "autoMessageTranslates": {
    "ru-RU": "",
    "en-US": ""
  }
}';

            $model->ccpc_theme_json = '{
  "theme": "linear-gradient(270deg, #0AAB99 0%, #1E71D1 100%)",
  "primary": "#0C89DF",
  "primaryDark": "#0066BA",
  "accent": "#0C89DF",
  "accentDark": "#0066BA"
}';

            $model->ccpc_registration_json = '{
  "enabled": true,
  "departments": [
    "Sales",
    "Support"
  ],
  "registrationTitle": "Registration title if registration is enabled",
  "registrationSubtitle": "Registration subtitle if it is enabled",
  "formFields": {
    "name": {
      "enabled": true,
      "required": true,
      "maxLength": 40,
      "minLength": 3
    },
    "email": {
      "enabled": true,
      "required": true,
      "maxLength": 40,
      "minLength": 3
    },
    "department": {
      "enabled": true,
      "required": true
    }
  }
}';

            $model->ccpc_settings_json = '{
  "fileUpload": true,
  "maxMessageLength": 500
}';
        }

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
