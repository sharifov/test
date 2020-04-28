<?php

namespace frontend\controllers;

use common\models\ClientProject;
use common\models\UserContactList;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\model\user\entity\Access;
use Yii;
use common\models\Client;
use common\models\search\ContactsSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ContactsController implements the CRUD actions for Client model.
 */
class ContactsController extends FController
{
    /**
     * @return array
     */
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactsSearch(Auth::id());

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
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
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Client();
        $post = Yii::$app->request->post($model->formName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $userContactList = new UserContactList();
            $userContactList->ucl_client_id = $model->id;
            $userContactList->ucl_user_id = Auth::id();

            if(!$userContactList->save()) {
                Yii::error(VarDumper::dumpAsString($userContactList->errors),
                    'ContactsController:actionCreate:saveUserContactList');
            }

            if(isset($post['projects'])) {
                foreach ($post['projects'] as $projectId) {
                    $clientProject = new ClientProject();
                    $clientProject->cp_client_id = $model->id;
                    $clientProject->cp_project_id = (int) $projectId;
                    $clientProject->save();
                    if(!$clientProject->save()) {
                        Yii::error(VarDumper::dumpAsString($clientProject->errors),
                            'ContactsController:actionCreate:saveClientProject');
                    }
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post($model->formName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if(isset($post['projects'])) {
                ClientProject::deleteAll(['cp_client_id' => $model->id]);
                foreach ($post['projects'] as $projectId) {
                    $clientProject = new ClientProject();
                    $clientProject->cp_client_id = $model->id;
                    $clientProject->cp_project_id = (int) $projectId;
                    $clientProject->save();
                    if(!$clientProject->save()) {
                        Yii::error(VarDumper::dumpAsString($clientProject->errors),
                            'ContactsController:actionUpdate:saveClientProject');
                    }
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Client model.
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
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
