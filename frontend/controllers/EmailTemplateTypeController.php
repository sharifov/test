<?php

namespace frontend\controllers;

use Yii;
use common\models\EmailTemplateType;
use common\models\search\EmailTemplateTypeSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * EmailTemplateTypeController implements the CRUD actions for EmailTemplateType model.
 */
class EmailTemplateTypeController extends FController
{
    /**
     * {@inheritdoc}
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'synchronization'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    /*[
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],*/
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all EmailTemplateType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmailTemplateTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailTemplateType model.
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
     * Creates a new EmailTemplateType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmailTemplateType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->etp_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmailTemplateType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->etp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EmailTemplateType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmailTemplateType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailTemplateType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmailTemplateType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionSynchronization() : Response
    {
        $result = EmailTemplateType::synchronizationTypes();

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
