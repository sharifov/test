<?php

namespace frontend\controllers;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Client;
use common\models\ClientPhone;
use sales\model\sms\entity\smsDistributionList\forms\SmsDistributionListAddMultipleForm;
use Yii;
use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use sales\model\sms\entity\smsDistributionList\search\SmsDistributionListSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SmsDistributionListController implements the CRUD actions for SmsDistributionList model.
 */
class SmsDistributionListController extends FController
{

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
     * Lists all SmsDistributionList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SmsDistributionListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SmsDistributionList model.
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
     * Creates a new SmsDistributionList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SmsDistributionList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sdl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new SmsDistributionList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateMultiple()
    {
        $model = new SmsDistributionListAddMultipleForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() ){


            $phoneListTo = explode(',' , $model->sdl_phone_to_list);
            $phoneListToValidate = [];

            if ($phoneListTo) {
                foreach ($phoneListTo as $phone) {
                    $phone = trim($phone);
                    if ($phone) {
                        $phoneListToValidate[] = $phone;
                    }
                }
            }

            if ($phoneListToValidate) {
                $successItems = [];
                $warningItems = [];

                foreach ($phoneListToValidate as $phoneTo) {
                    $smsModel = new SmsDistributionList();
                    $smsModel->sdl_status_id = $model->sdl_status_id;
                    $smsModel->sdl_phone_from = $model->sdl_phone_from;
                    $smsModel->sdl_phone_to = $phoneTo;
                    $smsModel->sdl_priority = $model->sdl_priority;
                    $smsModel->sdl_end_dt = $model->sdl_end_dt;
                    $smsModel->sdl_start_dt = $model->sdl_start_dt;
                    $smsModel->sdl_text = $model->sdl_text;
                    $smsModel->sdl_project_id = $model->sdl_project_id;

                    if($smsModel->save()) {
                        $successItems[] = Html::encode($smsModel->sdl_phone_to);
                    } else {
                        $warningItems[] = Html::encode($smsModel->sdl_phone_to) . ' - ' . VarDumper::dumpAsString($smsModel->errors);
                    }
                }

                if($successItems) {
                    Yii::$app->session->setFlash('success', 'Success added phones: ' . implode(', ', $successItems));
                }

                if($warningItems) {
                    Yii::$app->session->setFlash('warning', 'Warning: ' . implode(', ', $warningItems));
                }

                return $this->redirect(['index']);
            }


            /*$smsModel->attributes = $model->attributes;
            if($smsModel->save()}) {

            }*/
        }

        return $this->render('create-multiple', [
            'model' => $model,
        ]);
    }


    /**
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSend()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if ($model->sendSms()) {
            Yii::$app->session->setFlash('success', 'Success send to communication: Id: ' . $model->sdl_id);
        } else {
            Yii::$app->session->setFlash('danger', 'Error send to communication: Id: ' . $model->sdl_id);
        }

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing SmsDistributionList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sdl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SmsDistributionList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SmsDistributionList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SmsDistributionList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): SmsDistributionList
    {
        if (($model = SmsDistributionList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
