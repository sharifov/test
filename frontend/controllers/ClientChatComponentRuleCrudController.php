<?php

namespace frontend\controllers;

use Yii;
use src\model\clientChat\componentRule\entity\ClientChatComponentRule;
use src\model\clientChat\componentRule\entity\search\ClientChatComponentRuleSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class ClientChatComponentRuleCrudController extends FController
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
        $searchModel = new ClientChatComponentRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cccr_component_event_id
     * @param string $cccr_value
     * @param string $cccr_runnable_component
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cccr_component_event_id, $cccr_value, $cccr_runnable_component): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cccr_component_event_id, $cccr_value, $cccr_runnable_component),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatComponentRule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cccr_component_event_id' => $model->cccr_component_event_id, 'cccr_value' => $model->cccr_value, 'cccr_runnable_component' => $model->cccr_runnable_component]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cccr_component_event_id
     * @param string $cccr_value
     * @param string $cccr_runnable_component
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cccr_component_event_id, $cccr_value, $cccr_runnable_component)
    {
        $model = $this->findModel($cccr_component_event_id, $cccr_value, $cccr_runnable_component);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cccr_component_event_id' => $model->cccr_component_event_id, 'cccr_value' => $model->cccr_value, 'cccr_runnable_component' => $model->cccr_runnable_component]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cccr_component_event_id
     * @param string $cccr_value
     * @param string $cccr_runnable_component
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cccr_component_event_id, $cccr_value, $cccr_runnable_component): Response
    {
        $this->findModel($cccr_component_event_id, $cccr_value, $cccr_runnable_component)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cccr_component_event_id
     * @param string $cccr_value
     * @param string $cccr_runnable_component
     * @return ClientChatComponentRule
     * @throws NotFoundHttpException
     */
    protected function findModel($cccr_component_event_id, $cccr_value, $cccr_runnable_component): ClientChatComponentRule
    {
        if (($model = ClientChatComponentRule::findOne(['cccr_component_event_id' => $cccr_component_event_id, 'cccr_value' => $cccr_value, 'cccr_runnable_component' => $cccr_runnable_component])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
