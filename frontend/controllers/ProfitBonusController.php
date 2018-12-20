<?php

namespace frontend\controllers;

use Yii;
use common\models\ProfitBonus;
use common\models\search\ProfitBonusSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Employee;

/**
 * ProfitBonusController implements the CRUD actions for ProfitBonus model.
 */
class ProfitBonusController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProfitBonus models.
     * @return mixed
     */
    public function actionIndex($user_id = null)
    {
        $searchModel = new ProfitBonusSearch();
        $params = Yii::$app->request->queryParams;
        if($user_id !== null){
            $params['ProfitBonusSearch']['pb_user_id'] = $user_id;
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Displays a single ProfitBonus model.
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

    public function actionCreate($user_id = null)
    {
        $model = new ProfitBonus();
        if($user_id !== null){
            $model->pb_user_id = $user_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->pb_updated_user_id = Yii::$app->user->id;
            if($model->save()){
                return $this->redirect(['index','user_id' => $user_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProfitBonus model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id,$user_id = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->pb_updated_user_id = Yii::$app->user->id;
            if($model->save()) {
                return $this->redirect(['index','user_id' => $user_id]);
            }

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProfitBonus model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id,$user_id = null)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index','user_id' => $user_id]);
    }

    /**
     * Finds the ProfitBonus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProfitBonus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProfitBonus::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
