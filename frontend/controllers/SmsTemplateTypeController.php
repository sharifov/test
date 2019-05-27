<?php

namespace frontend\controllers;

use Yii;
use common\models\SmsTemplateType;
use common\models\search\SmsTemplateTypeSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SmsTemplateTypeController implements the CRUD actions for SmsTemplateType model.
 */
class SmsTemplateTypeController extends FController
{

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
     * Lists all SmsTemplateType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SmsTemplateTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SmsTemplateType model.
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
     * Creates a new SmsTemplateType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SmsTemplateType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->stp_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SmsTemplateType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->stp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SmsTemplateType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SmsTemplateType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SmsTemplateType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return Response
     * @throws \yii\httpclient\Exception
     */
    public function actionSynchronization() : Response
    {
        $result = SmsTemplateType::synchronizationTypes();

        if($result) {
            if($result['error']) {
                Yii::$app->getSession()->setFlash('error', $result['error']);
            } else {
                $message = 'Synchronization successful<br>';
                if($result['created']) {
                    $message .= 'Created type: "'.implode(', ', $result['created']).'"<br>';
                }
                if($result['updated']) {
                    $message .= 'Updated type: "'.implode(', ', $result['updated']).'"<br>';
                }
                Yii::$app->getSession()->setFlash('success', $message);
            }
        }

        return $this->redirect(['index']);
    }
}
