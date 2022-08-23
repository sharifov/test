<?php

namespace frontend\controllers;

use src\model\clientChatForm\entity\abac\ClientChatFormAbacObject;
use Yii;
use src\model\clientChatForm\entity\ClientChatForm;
use src\model\clientChatForm\entity\ClientChatFormSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class ClientChatFormCrudController
 */
class ClientChatFormCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ClientChatFormSearch();
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
        $model = new ClientChatForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ccf_id]);
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
        /** @abac ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_UPDATE, Access to update client chat form */
        if (!Yii::$app->abac->can(null, ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_UPDATE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ccf_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionBuilder($id)
    {
        /** @abac ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_BUILDER, Access to builder client chat form */
        if (!Yii::$app->abac->can(null, ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_BUILDER)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ccf_id]);
        }

        return $this->render('builder', [
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
        /** @abac ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_DELETE,  Access to delete client chat form */
        if (!Yii::$app->abac->can(null, ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_DELETE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return ClientChatForm
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ClientChatForm
    {
        if (($model = ClientChatForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
