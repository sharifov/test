<?php

namespace frontend\controllers;

use sales\model\airportLang\entity\AirportLangSearch;
use Yii;
use common\models\Airports;
use common\models\search\AirportsSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AirportsController implements the CRUD actions for Airports model.
 */
class AirportsController extends FController
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
                    'delete' => ['POST']
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
     * Lists all Airports models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AirportsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Airports model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $airport = $this->findModel($id);

        $searchModel = new AirportLangSearch();
        $params[$searchModel->formName()] = ['ail_iata' => $airport->iata];
        $dataProvider = $searchModel->search($params);
        $dataProvider->sort = false;

        return $this->render('view', [
            'model' => $airport,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Airports model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Airports();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->iata]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Airports model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->iata]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Airports model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionSynchronization(): \yii\web\Response
    {

        // $lastRecord = Airports::find()->select('a_updated_dt')->orderBy(['a_updated_dt' => SORT_ASC])->limit(1)->one();
        // $lastUpdated = ($lastRecord && $lastRecord->a_updated_dt) ? strtotime($lastRecord->a_updated_dt) : 0;

        $lastUpdated = Yii::$app->request->get('lastUpdated');
        if ($lastUpdated) {
            $lastUpdated = strtotime($lastUpdated);
        }

        set_time_limit(300);

        $result = Airports::synchronization(10000, $lastUpdated);

        if ($result) {
            if ($result['error']) {
                Yii::$app->getSession()->setFlash('error', $result['error']);
            } else {
                if ($result['info']) {
                    $message = '<b>Information</b>:<br>';
                    $message .= implode(', ', $result['info']);
                    Yii::$app->getSession()->setFlash('info', $message);
                }

                if ($result['created']) {
                    $message = 'Synchronization successful: created<br>';
                    $message .= 'Created Airports (' . count($result['created']) . '): "' . implode(', ', $result['created']);
                    Yii::$app->getSession()->setFlash('success', $message);
                }
                if ($result['updated']) {
                    $message = 'Synchronization successful: updated<br>';
                    $message .= 'Updated Airports (' . count($result['updated']) . '): "' . implode(', ', $result['updated']);
                    Yii::$app->getSession()->setFlash('warning', $message);
                }
                if ($result['errored']) {
                    $message = 'Synchronization error<br>';
                    $message .= 'Errored Airports (' . count($result['errored']) . '): "' . implode(', ', $result['errored']);
                    Yii::$app->getSession()->setFlash('error', $message);
                }

                if ($result['deleted']) {
                    $message = 'Need for deleted<br>';
                    $message .= 'Need Deleted Airports (' . count($result['deleted']) . '): "' . implode(', ', $result['deleted']);
                    Yii::$app->getSession()->setFlash('danger', $message);
                }
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Airports model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Airports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Airports::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
