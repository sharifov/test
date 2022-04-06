<?php

namespace modules\requestControl\controllers;

use yii\base\Response;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use modules\requestControl\models\RequestControlRule;
use modules\requestControl\models\search\RequestControlRuleSearch;
// NOTICE: Should be apart if it possible. Temporary here for `frontend`
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;

/**
 * Class ManageController
 *
 * Controller which implements actions
 *
 * NOTE: This module extends `frontend\controllers\FController`, consequently depends of `frontend`.
 *
 * TODO: Make solution for apart the module from another apps and excess dependencies (if is possible)
 */
class ManageController extends FController
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(
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
     * Index page with table of all exist items in table
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new RequestControlRuleSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View page for specific item by received id
     *
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * New item create page
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new RequestControlRule();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->rcr_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Page for updating the item by id
     *
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->rcr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Item deleting function. In this case it used asynchronously by `ajax`.
     *
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this
            ->findModel($id)
            ->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return RequestControlRule
     * @throws NotFoundHttpException
     */
    protected function findModel($id): RequestControlRule
    {
        if (($model = RequestControlRule::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
