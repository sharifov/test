<?php

namespace frontend\controllers;

use sales\model\userAuthClient\entity\UserAuthClient;
use sales\model\userAuthClient\entity\UserAuthClientSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserAuthClientCrudController implements the CRUD actions for UserAuthClient model.
 */
class UserAuthClientCrudController extends FController
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
     * Lists all UserAuthClient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAuthClientSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserAuthClient model.
     * @param int $uac_id Ac ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($uac_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($uac_id),
        ]);
    }

    /**
     * Creates a new UserAuthClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserAuthClient();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'uac_id' => $model->uac_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserAuthClient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $uac_id Ac ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($uac_id)
    {
        $model = $this->findModel($uac_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'uac_id' => $model->uac_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserAuthClient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $uac_id Ac ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($uac_id)
    {
        $this->findModel($uac_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserAuthClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $uac_id Ac ID
     * @return UserAuthClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($uac_id)
    {
        if (($model = UserAuthClient::findOne($uac_id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
