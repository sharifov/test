<?php

namespace frontend\controllers;

use Yii;
use sales\model\clientAccountSocial\entity\ClientAccountSocial;
use sales\model\clientAccountSocial\entity\ClientAccountSocialSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class ClientAccountSocialCrudController
 */
class ClientAccountSocialCrudController extends FController
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
        $searchModel = new ClientAccountSocialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cas_ca_id
     * @param integer $cas_type_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cas_ca_id, $cas_type_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cas_ca_id, $cas_type_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientAccountSocial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cas_ca_id' => $model->cas_ca_id, 'cas_type_id' => $model->cas_type_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cas_ca_id
     * @param integer $cas_type_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cas_ca_id, $cas_type_id)
    {
        $model = $this->findModel($cas_ca_id, $cas_type_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cas_ca_id' => $model->cas_ca_id, 'cas_type_id' => $model->cas_type_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cas_ca_id
     * @param integer $cas_type_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cas_ca_id, $cas_type_id): Response
    {
        $this->findModel($cas_ca_id, $cas_type_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cas_ca_id
     * @param integer $cas_type_id
     * @return ClientAccountSocial
     * @throws NotFoundHttpException
     */
    protected function findModel($cas_ca_id, $cas_type_id): ClientAccountSocial
    {
        if (($model = ClientAccountSocial::findOne(['cas_ca_id' => $cas_ca_id, 'cas_type_id' => $cas_type_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
