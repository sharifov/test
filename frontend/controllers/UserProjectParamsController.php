<?php

namespace frontend\controllers;

use http\Url;
use Yii;
use common\models\UserProjectParams;
use common\models\search\UserProjectParamsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserProjectParamsController implements the CRUD actions for UserProjectParams model.
 */
class UserProjectParamsController extends FController
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
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all UserProjectParams models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserProjectParamsSearch();

        $params = Yii::$app->request->queryParams;

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['UserProjectParamsSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserProjectParams model.
     * @param integer $upp_user_id
     * @param integer $upp_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($upp_user_id, $upp_project_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($upp_user_id, $upp_project_id),
        ]);
    }

    /**
     * Creates a new UserProjectParams model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserProjectParams([
            'upp_allow_general_line' => true,
            'upp_allow_transfer' => true,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->post('redirect')) {
                return $this->redirect(Yii::$app->request->post('redirect'));
            } else {
                return $this->redirect(['view', 'upp_user_id' => $model->upp_user_id, 'upp_project_id' => $model->upp_project_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionCreateAjax()
    {
        $model = new UserProjectParams([
            'upp_allow_general_line' => true,
            'upp_allow_transfer' => true
        ]);
        $model->upp_user_id = Yii::$app->request->get('user_id');

        //VarDumper::dump(Yii::$app->request->post(), 10, true); exit;

        if ($model->load(Yii::$app->request->post())) {
            //$url = \yii\helpers\Url::to(Yii::$app->request->post('redirect'));

            //VarDumper::dump($url, 10, true); exit;
            //\Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->save()) {
                return 'Success <script>$("#modal-df").modal("hide")</script>';
                //Yii::$app->session->setFlash('success', 'Created new project params!');
                //return $this->redirect(Yii::$app->request->referrer); //'/'.Yii::$app->request->post('redirect'));
            }
        }

        return $this->renderAjax('create_ajax', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserProjectParams model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $upp_user_id
     * @param integer $upp_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($upp_user_id, $upp_project_id)
    {
        $model = $this->findModel($upp_user_id, $upp_project_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                return $this->redirect(Yii::$app->request->referrer);
            }

            return $this->redirect(['view', 'upp_user_id' => $model->upp_user_id, 'upp_project_id' => $model->upp_project_id]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create_ajax', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdateAjax()
    {
        $data = Yii::$app->request->get('data');

        $upp_user_id = $data['upp_user_id'] ?? 0;
        $upp_project_id = $data['upp_project_id'] ?? 0;


        $model = $this->findModel($upp_user_id, $upp_project_id);

        if ($model->load(Yii::$app->request->post())) {
            //if(Yii::$app->request->isAjax) {
            if ($model->save()) {
                //$this->view->registerJs('$("#activity-modal").modal("hide");');
                return 'Success <script>$("#modal-df").modal("hide")</script>';

                //return 'OK'; //$this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('update_ajax', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserProjectParams model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $upp_user_id
     * @param integer $upp_project_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($upp_user_id, $upp_project_id)
    {
        $model = $this->findModel($upp_user_id, $upp_project_id);
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the UserProjectParams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $upp_user_id
     * @param integer $upp_project_id
     * @return UserProjectParams the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($upp_user_id, $upp_project_id)
    {
        if (($model = UserProjectParams::findOne(['upp_user_id' => $upp_user_id, 'upp_project_id' => $upp_project_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
