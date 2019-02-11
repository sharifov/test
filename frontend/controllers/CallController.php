<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Project;
use common\models\UserProjectParams;
use Yii;
use common\models\Call;
use common\models\search\CallSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallController implements the CRUD actions for Call model.
 */
class CallController extends FController
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'inbox', 'soft-delete', 'list', 'user-map', 'all-read'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Call models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CallSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Call models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new CallSearch();

        $params = Yii::$app->request->queryParams;
        $params['CallSearch']['c_created_user_id'] = Yii::$app->user->id;

        $dataProvider = $searchModel->searchAgent($params);

        $phoneList = Employee::getPhoneList(Yii::$app->user->id);
        $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);


        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'phoneList'          => $phoneList,
            'projectList'       => $projectList,
        ]);
    }


    /**
     * Displays a single Call model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);


        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Call model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView2($id)
    {

        $model = $this->findModel($id);
        $this->checkAccess($model);

        if($model->c_is_new) {
            //$model->c_read_dt = date('Y-m-d H:i:s');
            $model->c_is_new = false;
            $model->save();
        }

        return $this->render('view2', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Call model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Call();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->c_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Call model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->c_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Call model.
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
     * Finds the Call model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Call the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Call::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * @param Call $model
     * @throws ForbiddenHttpException
     */
    protected function checkAccess(Call $model) : void
    {
        /*$phoneList = [];

        $phoneList[$model->c_to] = $model->c_to;
        $phoneList[$model->c_from] = $model->c_from;

        $access = UserProjectParams::find()->where(['upp_user_id' => Yii::$app->user->id])
            ->andWhere(['or', ['upp_tw_phone_number' => $phoneList], ['upp_phone_number' => $phoneList]])->exists();*/


        $access = $model->c_created_user_id == Yii::$app->user->id ? true : false;


        if(!$access) {
            throw new ForbiddenHttpException('Access denied for this Call. '); // Check User Project Params phones
        }
    }


    /**
     * @return \yii\web\Response
     */
    public function actionAllRead()
    {
        Call::updateAll(['c_is_new' => false], ['c_is_new' => true, 'c_created_user_id' => Yii::$app->user->id]);
        return $this->redirect(['list']);
    }


    public function actionUserMap()
    {
        $projects = Project::getList();
        $usersByProject = [];

        if($projects) {
            foreach ($projects as $projectId => $projectName) {

                $query = Employee::getQueryAgentOnlineStatus(Yii::$app->user->id, $projectId);
                $usersByProject[$projectId]['project_name'] = $projectName;
                $usersByProject[$projectId]['project_id'] = $projectId;
                $usersByProject[$projectId]['users'] = $query->asArray()->all();
            }
        }

        //VarDumper::dump($usersByProject, 10, true);

        return $this->render('user-map', [
            'usersByProject' => $usersByProject,
        ]);
    }
}
