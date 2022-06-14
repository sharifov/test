<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use Yii;
use common\models\ClientProject;
use common\models\search\ClientProjectSearch;
use frontend\models\form\ClientProjectForm;
use yii\base\Response;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * ClientProjectController implements the CRUD actions for ClientProject model.
 */
class ClientProjectController extends FController
{
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
            'access' => [
                'allowActions' => [
                    'unsubscribe-client-ajax',
                    'subscribe-client-ajax',
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
     * Lists all ClientProject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientProject model.
     * @param integer $cp_client_id
     * @param integer $cp_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cp_client_id, $cp_project_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($cp_client_id, $cp_project_id),
        ]);
    }

    /**
     * Creates a new ClientProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClientProject();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cp_client_id' => $model->cp_client_id, 'cp_project_id' => $model->cp_project_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClientProject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $cp_client_id
     * @param integer $cp_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cp_client_id, $cp_project_id)
    {
        $model = $this->findModel($cp_client_id, $cp_project_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cp_client_id' => $model->cp_client_id, 'cp_project_id' => $model->cp_project_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClientProject model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $cp_client_id
     * @param integer $cp_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($cp_client_id, $cp_project_id)
    {
        $this->findModel($cp_client_id, $cp_project_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionUnsubscribeClientAjax(): Response
    {
        $form = new ClientProjectForm();
        if ($form->load(Yii::$app->request->get(), "") && $form->validate()) {
            $leadAbacDto = new LeadAbacDto(Lead::findOne($form->leadId), Auth::id());
            /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS, Access to action client unsubscribe*/
            if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_UNSUBSCRIBE)) {
                throw new ForbiddenHttpException('Access denied.');
            }

            ClientProject::unSubScribe($form->clientId, $form->projectId, $form->action);

            return $this->asJson(['data' => ['action' => $form->action]]);
        }
        throw new BadRequestHttpException('The parameters clientId, projectId, action are required ');
    }

    public function actionSubscribeClientAjax(): Response
    {
        $form = new ClientProjectForm();
        if ($form->load(Yii::$app->request->get(), "") && $form->validate()) {
            $leadAbacDto = new LeadAbacDto(Lead::findOne($form->leadId), Auth::id());

            /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SUBSCRIBE, Access to action client subscribe*/
            if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SUBSCRIBE)) {
                throw new ForbiddenHttpException('Access denied.');
            }

            ClientProject::unSubScribe($form->clientId, $form->projectId, $form->action);

            return $this->asJson(['data' => ['action' => $form->action]]);
        }

        throw new BadRequestHttpException('The parameters clientId, projectId, action are required ');
    }

    /**
     * Finds the ClientProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $cp_client_id
     * @param integer $cp_project_id
     * @return ClientProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cp_client_id, $cp_project_id)
    {
        if (($model = ClientProject::findOne(['cp_client_id' => $cp_client_id, 'cp_project_id' => $cp_project_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
