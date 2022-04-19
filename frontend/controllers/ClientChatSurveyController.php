<?php

namespace frontend\controllers;

use common\models\ClientChatSurvey;
use common\models\search\ClientChatSurveySearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class ClientChatSurveyController
 * @package frontend\controllers
 */
class ClientChatSurveyController extends FController
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientChatSurveySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $ccs_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($ccs_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ccs_id)
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ClientChatSurvey();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccs_id' => $model->ccs_id]);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $ccs_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ccs_id)
    {
        $model = $this->findModel($ccs_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccs_id' => $model->ccs_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $ccs_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($ccs_id)
    {
        $this->findModel($ccs_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $ccs_id
     * @return ClientChatSurvey|null
     * @throws NotFoundHttpException
     */
    protected function findModel($ccs_id)
    {
        if (($model = ClientChatSurvey::findOne(['ccs_id' => $ccs_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
