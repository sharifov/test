<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use common\models\search\EmployeeSearch;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\UserProjectParams;
use Yii;
use common\models\Call;
use common\models\search\CallSearch;
use yii\db\Expression;
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


    /**
     * @return string
     * @throws \Exception
     */
    public function actionAutoRedial()
    {

        /** @var Employee $user */
        $user = Yii::$app->user->identity;



        /*if(Yii::$app->request->get('act')) {
            $profile = $user->userProfile;
            if($profile) {
                $profile->up_updated_dt = date('Y-m-d H:i:s');

                if (Yii::$app->request->get('act') == 'start') {
                    $profile->up_auto_redial = true;
                    $profile->save();
                }
                if (Yii::$app->request->get('act') == 'stop') {
                    $profile->up_auto_redial = false;
                    $profile->save();
                }
            }
        }*/


        //$callModel = null;
        $leadModel = null;
        $callData = [];


        $query = Lead::getPendingQuery();
        $allPendingLeadsCount = $query->count();

        $query = Lead::getPendingQuery($user->id);
        $myPendingLeadsCount = $query->count();


        $callModel = Call::find()->where(['c_call_status' => [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]])->andWhere(['c_created_user_id' => Yii::$app->user->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

        //echo Call::find()->where(['c_call_status' => [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]])->andWhere(['c_created_user_id' => Yii::$app->user->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->createCommand()->getRawSql(); exit;

        if(Yii::$app->request->get('act') === 'find') {



            $query = Lead::getPendingQuery($user->id);
            $query->limit(1);

            //echo $query->createCommand()->getRawSql(); exit;

            //$leadModel = $query->one();

            if(!$callModel) {
                $leadModel = $query->one();
            }

            if($leadModel) {
                $callData['error'] = null;
                $callData['project_id'] = $leadModel->project_id;
                $callData['lead_id'] = $leadModel->id;
                $callData['phone_to'] = null;
                $callData['phone_from'] = null;

                if($leadModel->client && $leadModel->client->clientPhones) {
                    foreach ($leadModel->client->clientPhones as $phone) {
                        if(!$phone->phone) {
                            continue;
                        }
                        $callData['phone_to'] = trim($phone->phone);
                        break;
                    }
                }

                $upp = UserProjectParams::find()->where(['upp_project_id' => $leadModel->project_id, 'upp_user_id' => $user->id])->one();
                if($upp && $upp->upp_tw_phone_number) {
                    $callData['phone_from'] = $upp->upp_tw_phone_number;
                }

                if(!$callData['phone_from']) {
                    $callData['error'] = 'Not found phone number for project ('.$leadModel->project->name.')';
                }

                if(!$callData['phone_to']) {
                    $callData['error'] = 'Not found client number';
                }
                //$callData['error'] = 'Not found client number';

                //$callData['phone_to'] = '+37369594567';
            }


            $isActionFind = true;
        } else {
            $isActionFind = false;
        }


        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        //$params2 = Yii::$app->request->post();
        //$params = array_merge($params, $params2);

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }


        $checkShiftTime = true;

        if($isAgent) {
            $checkShiftTime = $user->checkShiftTime();
            /*$userParams = $user->userParams;

            if($userParams) {
                if($userParams->up_inbox_show_limit_leads > 0) {
                    $params['LeadSearch']['limit'] = $userParams->up_inbox_show_limit_leads;
                }
            }*/


            /*if($checkShiftTime = !$user->checkShiftTime()) {
                throw new ForbiddenHttpException('Access denied! Invalid Agent shift time');
            }*/
        }

        //$checkShiftTime = true;



        //$leadModel = new Lead();


        /*if(Yii::$app->request->isPjax) {
            $leadModel = Lead::find()->orderBy(['id' => SORT_DESC])->limit(1)->one();
        } else {
            $leadModel = null; //Lead::findOne(22);
        }*/


        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }


        if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
            $dataProvider = $searchModel->searchInbox($params);
        } else {
            $dataProvider = null;
        }



        $isAccessNewLead = $user->accessTakeNewLead();
        $accessLeadByFrequency = [];

        if($isAccessNewLead){
            $accessLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
            if(!$accessLeadByFrequency['access']){
                $isAccessNewLead = $accessLeadByFrequency['access'];
            }
        }

        /*$dataProviderSegments = null;

        if($leadModel) {
            $searchModelSegments = new LeadFlightSegmentSearch();

            $params = Yii::$app->request->queryParams;
            //if($leadModel) {
            $params['LeadFlightSegmentSearch']['lead_id'] = $leadModel ? $leadModel->id : 0;
            //}
            $dataProviderSegments = $searchModelSegments->search($params);
        }*/


        //echo $callModel->c_id; exit;


        $searchModelCall = new CallSearch();

        $params = Yii::$app->request->queryParams;
        $params['CallSearch']['c_created_user_id'] = Yii::$app->user->id;
        $params['CallSearch']['limit'] = 10;

        $dataProviderCall = $searchModelCall->searchAgent($params);
        $projectList = Project::getListByUser(Yii::$app->user->id);


        return $this->render('auto-redial', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkShiftTime' => $checkShiftTime,
            'isAgent' => $isAgent,
            'isAccessNewLead' => $isAccessNewLead,
            'accessLeadByFrequency' => $accessLeadByFrequency,
            'user' => $user,

            'leadModel' => $leadModel,
            'callModel' => $callModel,
            //'searchModelSegments' => $searchModelSegments,
            //'dataProviderSegments' => $dataProviderSegments,
            'isActionFind' => $isActionFind,
            'callData' => $callData,
            'myPendingLeadsCount' => $myPendingLeadsCount,
            'allPendingLeadsCount' => $allPendingLeadsCount,

            //'searchModelCall' => $searchModelCall,
            'dataProviderCall' => $dataProviderCall,
            'projectList'       => $projectList,
        ]);

    }
}
