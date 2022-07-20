<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\search\QuotePriceSearch;
use common\models\UserProjectParams;
use frontend\models\CommunicationForm;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Yii;
use common\models\Quote;
use common\models\search\QuoteSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteController implements the CRUD actions for Quote model.
 */
class QuotesController extends FController
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
     * Lists all Quote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['reset'])) {
            unset($params['QuoteSearch']['date_range']);
        }

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Quote model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        $searchModel = new QuotePriceSearch();

        $params = Yii::$app->request->queryParams;

        $params['QuotePriceSearch']['quote_id'] = $model->id;

        $dataProvider = $searchModel->search($params);

        //unset($searchModel);
        // VarDumper::dump($quotes, 10, true);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxDetails()
    {
        $id = Yii::$app->request->get('id');

        $model = $this->findModel($id);
        $lead = $model->lead;

        if ($lead->status === Lead::STATUS_TRASH && Auth::user()->isAgent()) {
            throw new ForbiddenHttpException('Access Denied for Agent');
        }

        //unset($searchModel);
        //VarDumper::dump($model, 10, true);

        return $this->renderPartial('view-details', [
            'model' => $model,
        ]);
    }

    public function actionAjaxCapture()
    {
        $quoteID = Yii::$app->request->get('id');
        $gid = mb_substr(Yii::$app->request->get('gid'), 0, 32);
        $lead = Lead::find()->where(['gid' => $gid])->limit(1)->one();

        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;

        $tpl = 'chat_offer';
        $language = Yii::$app->language ?: 'en-US';
        $mailFrom = '';
        $mailTo = '';

        $project = $lead->project;
        $projectContactInfo = [];

        if ($project && $project->contact_info) {
            $projectContactInfo = @json_decode($project->contact_info, true);
        }

        $content_data = $lead->getEmailData2([$quoteID], $projectContactInfo);

        try {
            $mailCapture = $communication->mailCapture(
                $lead->project_id,
                $tpl,
                $mailFrom,
                $mailTo,
                $content_data,
                $language,
                [
                    'img_width' => 265,
                    'img_height' => 60,
                    'img_format' => 'png',
                    'img_update' => 1,
                ]
            );

            if (!isset($mailCapture['data']['img'])) {
                throw new \RuntimeException('Create capture error.');
            }

            return  $mailCapture['data']['img'];
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString([
                'error' => AppHelper::throwableFormatter($e),
                'quoteId' => $quoteID,
            ]), 'QuotesController:actionAjaxCapture');
            return $e->getMessage();
        }
    }


    /**
     * Creates a new Quote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Quote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Quote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Quote model.
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
     * Finds the Quote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Quote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
