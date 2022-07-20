<?php

namespace frontend\controllers;

use src\model\clientUserReturn\entity\ClientUserReturn;
use src\model\clientUserReturn\entity\ClientUserReturnSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientUserReturnCrudController implements the CRUD actions for ClientUserReturn model.
 */
class ClientUserReturnCrudController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ClientUserReturn models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientUserReturnSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientUserReturn model.
     * @param int $cur_client_id Cur Client ID
     * @param int $cur_user_id Cur User ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cur_client_id, $cur_user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($cur_client_id, $cur_user_id),
        ]);
    }

    /**
     * Creates a new ClientUserReturn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ClientUserReturn();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'cur_client_id' => $model->cur_client_id, 'cur_user_id' => $model->cur_user_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClientUserReturn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $cur_client_id Cur Client ID
     * @param int $cur_user_id Cur User ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cur_client_id, $cur_user_id)
    {
        $model = $this->findModel($cur_client_id, $cur_user_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cur_client_id' => $model->cur_client_id, 'cur_user_id' => $model->cur_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClientUserReturn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $cur_client_id Cur Client ID
     * @param int $cur_user_id Cur User ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($cur_client_id, $cur_user_id)
    {
        $this->findModel($cur_client_id, $cur_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ClientUserReturn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $cur_client_id Cur Client ID
     * @param int $cur_user_id Cur User ID
     * @return ClientUserReturn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cur_client_id, $cur_user_id)
    {
        if (($model = ClientUserReturn::findOne(['cur_client_id' => $cur_client_id, 'cur_user_id' => $cur_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
