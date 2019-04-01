<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Project;
use common\models\search\EmployeeSearch;
use common\models\search\LeadSearch;
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
                        'actions' => ['index', 'update', 'view', 'inbox', 'soft-delete', 'list', 'user-map', 'all-read'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin', 'qa'],
                    ],

                    [
                        'actions' => ['delete', 'create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],

                    [
                        'actions' => ['view', 'view2', 'soft-delete', 'all-delete', 'all-read', 'list', 'auto-redial'],
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

        $params = Yii::$app->request->queryParams;
        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['CallSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->search($params);

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

        $this->layout = '@frontend/themes/gentelella/views/layouts/main_tv';

        $userId = Yii::$app->user->id;

        $searchModel = new CallSearch();
        $params = Yii::$app->request->queryParams;

        //if (Yii::$app->authManager->getAssignment('supervision', $userId)) {
            //$params['CallSearch']['supervision_id'] = $userId;
            //$params['CallSearch']['status'] = Employee::STATUS_ACTIVE;
        //}

        $params['CallSearch']['statuses'] = [Call::CALL_STATUS_QUEUE];
        $dataProvider = $searchModel->searchUserCallMap($params);

        $params['CallSearch']['statuses'] = [Call::CALL_STATUS_IN_PROGRESS, Call::CALL_STATUS_RINGING];
        $dataProvider3 = $searchModel->searchUserCallMap($params);

        $params['CallSearch']['statuses'] = [Call::CALL_STATUS_COMPLETED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_FAILED, Call::CALL_STATUS_NO_ANSWER, Call::CALL_STATUS_CANCELED];
        $params['CallSearch']['limit'] = 12;
        $dataProvider2 = $searchModel->searchUserCallMap($params);

        //$searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        //$searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('user-map', [
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            //'searchModel' => $searchModel,
        ]);

    }



    public function actionAutoRedial()
    {

        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }


        $checkShiftTime = true;

        if($isAgent) {
            $user = Yii::$app->user->identity;
            $checkShiftTime = $user->checkShiftTime();
            $userParams = $user->userParams;

            if($userParams) {
                if($userParams->up_inbox_show_limit_leads > 0) {
                    $params['LeadSearch']['limit'] = $userParams->up_inbox_show_limit_leads;
                }
            }


            /*if($checkShiftTime = !$user->checkShiftTime()) {
                throw new ForbiddenHttpException('Access denied! Invalid Agent shift time');
            }*/
        }

        //$checkShiftTime = true;



        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchInbox($params);

        $user = Yii::$app->user->identity;

        $isAccessNewLead = $user->accessTakeNewLead();
        $accessLeadByFrequency = [];

        if($isAccessNewLead){
            $accessLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
            if(!$accessLeadByFrequency['access']){
                $isAccessNewLead = $accessLeadByFrequency['access'];
            }
        }

        return $this->render('auto-redial', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkShiftTime' => $checkShiftTime,
            'isAgent' => $isAgent,
            'isAccessNewLead' => $isAccessNewLead,
            'accessLeadByFrequency' => $accessLeadByFrequency,
            'user' => $user,
            'newLeadsCount' => $user->getCountNewLeadCurrentShift()
        ]);

    }
}
