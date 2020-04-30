<?php

namespace frontend\controllers;

use common\models\Conference;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use common\models\search\LeadSearch;
use common\models\search\UserConnectionSearch;
use common\models\Sources;
use common\models\UserProjectParams;
use frontend\widgets\CallBox;
use frontend\widgets\IncomingCallWidget;
use http\Exception\InvalidArgumentException;
use sales\auth\Auth;
use sales\helpers\call\CallHelper;
use sales\services\call\CallService;
use Yii;
use common\models\Call;
use common\models\search\CallSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CallController implements the CRUD actions for Call model.
 *
 * @property CallService $callService
 */
class CallController extends FController
{
    private $callService;

    public function __construct($id, $module, CallService $callService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->callService = $callService;
    }

    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    //'cancel' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex(): string
    {
        $searchModel = new CallSearch();

        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dataProvider = $searchModel->search($params, $user);

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
            ->andWhere(['upp_tw_phone_number' => $phoneList])->exists();*/


        $access = $model->c_created_user_id === Yii::$app->user->id ? true : false;


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

        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';

        /** @var Employee $user */
        $user = Yii::$app->user->identity;



        $searchModel = new CallSearch();
        $searchModel2 = new UserConnectionSearch();
        $params = Yii::$app->request->queryParams;

        //if (Yii::$app->user->identity->canRole('supervision')) {
            //$params['CallSearch']['supervision_id'] = $userId;
            //$params['CallSearch']['status'] = Employee::STATUS_ACTIVE;
        //}

        $accessDepartmentModels = $user->udDeps;

        if($accessDepartmentModels) {
            $accessDepartments = ArrayHelper::map($accessDepartmentModels, 'dep_id', 'dep_id');
        } else {
            $accessDepartments = [];
        }

        $isSuper = ($user->isSupervision() || $user->isExSuper() || $user->isSupSuper());

        if ($isSuper && !in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $userGroupsModel = $user->ugsGroups;

            if ($userGroupsModel) {
                $userGroups = ArrayHelper::map($userGroupsModel, 'ug_id', 'ug_id');
            } else {
                $userGroups = [];
            }

            $params['UserConnectionSearch']['ug_ids'] = $userGroups;
            $params['CallSearch']['ug_ids'] = $userGroups;
        }

        //VarDumper::dump($accessDepartments, 10, true); exit;


        if (!$accessDepartments || in_array(Department::DEPARTMENT_SALES, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SALES;
            $dataProviderOnlineDep1 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep1 = null;
        }

        if (!$accessDepartments || in_array(Department::DEPARTMENT_EXCHANGE, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_EXCHANGE;
            $dataProviderOnlineDep2 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep2 = null;
        }

        if (!$accessDepartments || in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SUPPORT;
            $dataProviderOnlineDep3 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep3 = null;
        }

        if (!$accessDepartments) {
            $params['UserConnectionSearch']['dep_id'] = 0;
            $dataProviderOnline = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnline = null;
        }

        $params['CallSearch']['dep_ids'] = $accessDepartments;
        $params['CallSearch']['status_ids'] = [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING, Call::STATUS_QUEUE, Call::STATUS_IVR, Call::STATUS_DELAY];
        $dataProvider3 = $searchModel->searchUserCallMap($params);

        $params['CallSearch']['status_ids'] = [Call::STATUS_COMPLETED, Call::STATUS_BUSY, Call::STATUS_FAILED, Call::STATUS_NO_ANSWER, Call::STATUS_CANCELED];
        $params['CallSearch']['limit'] = 10;
        $dataProvider2 = $searchModel->searchUserCallMapHistory($params);

        //$searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        //$searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('user-map/user-map', [
            'dataProviderOnlineDep1' => $dataProviderOnlineDep1,
            'dataProviderOnlineDep2' => $dataProviderOnlineDep2,
            'dataProviderOnlineDep3' => $dataProviderOnlineDep3,
            'dataProviderOnline' => $dataProviderOnline,


            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            //'searchModel' => $searchModel,
        ]);

    }

    public function actionUserMap2()
    {

        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $searchModel = new CallSearch();
        $searchModel2 = new UserConnectionSearch();
        $params = Yii::$app->request->queryParams;

        //if (Yii::$app->user->identity->canRole('supervision')) {
        //$params['CallSearch']['supervision_id'] = $userId;
        //$params['CallSearch']['status'] = Employee::STATUS_ACTIVE;
        //}

        $accessDepartmentModels = $user->udDeps;

        if($accessDepartmentModels) {
            $accessDepartments = ArrayHelper::map($accessDepartmentModels, 'dep_id', 'dep_id');
        } else {
            $accessDepartments = [];
        }

        $isSuper = ($user->isSupervision() || $user->isExSuper() || $user->isSupSuper());

        if ($isSuper && !in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $userGroupsModel = $user->ugsGroups;

            if ($userGroupsModel) {
                $userGroups = ArrayHelper::map($userGroupsModel, 'ug_id', 'ug_id');
            } else {
                $userGroups = [];
            }

            $params['UserConnectionSearch']['ug_ids'] = $userGroups;
            $params['CallSearch']['ug_ids'] = $userGroups;
        }

        //VarDumper::dump($accessDepartments, 10, true); exit;


        if (!$accessDepartments || in_array(Department::DEPARTMENT_SALES, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SALES;
            $dataProviderOnlineDep1 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep1 = null;
        }

        if (!$accessDepartments || in_array(Department::DEPARTMENT_EXCHANGE, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_EXCHANGE;
            $dataProviderOnlineDep2 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep2 = null;
        }

        if (!$accessDepartments || in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SUPPORT;
            $dataProviderOnlineDep3 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep3 = null;
        }

        if (!$accessDepartments) {
            $params['UserConnectionSearch']['dep_id'] = 0;
            $dataProviderOnline = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnline = null;
        }

        $params['CallSearch']['dep_ids'] = $accessDepartments;
        $params['CallSearch']['status_ids'] = [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING, Call::STATUS_QUEUE, Call::STATUS_IVR, Call::STATUS_DELAY];
        $dataProvider3 = $searchModel->searchUserCallMap($params);

        $params['CallSearch']['status_ids'] = [Call::STATUS_COMPLETED, Call::STATUS_BUSY, Call::STATUS_FAILED, Call::STATUS_NO_ANSWER, Call::STATUS_CANCELED];
        $params['CallSearch']['limit'] = 10;
        $dataProvider2 = $searchModel->searchUserCallMapHistory($params);

        //$searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        //$searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('user-map2/user-map2', [
            'dataProviderOnlineDep1' => $dataProviderOnlineDep1,
            'dataProviderOnlineDep2' => $dataProviderOnlineDep2,
            'dataProviderOnlineDep3' => $dataProviderOnlineDep3,
            'dataProviderOnline' => $dataProviderOnline,


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


        $callModel = Call::find()->where(['c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]])->andWhere(['c_created_user_id' => $user->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

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

                $upp = UserProjectParams::find()->where(['upp_project_id' => $leadModel->project_id, 'upp_user_id' => $user->id])->withPhoneList()->one();
//                if($upp && $upp->upp_tw_phone_number) {
                if($upp && $upp->getPhone()) {

                    //$callData['phone_from'] = $upp->upp_tw_phone_number;
                    //$callData['phone_from'] = $upp->$upp->getPhone();

//                    $dpp = DepartmentPhoneProject::find()->where(['dpp_project_id' => $leadModel->project_id])->limit(1)->one();
                    $dpp = DepartmentPhoneProject::find()->where(['dpp_project_id' => $leadModel->project_id])->withPhoneList()->limit(1)->one();
//                    if($dpp && $dpp->dpp_phone_number) {
                    if($dpp && $dpp->getPhone()) {
//                        $callData['phone_from'] = $dpp->dpp_phone_number;
                        $callData['phone_from'] = $dpp->getPhone();
                    } else {
                        Yii::error('Not found Project Source, Project Id: '. $leadModel->project_id, 'CallController:actionAutoRedial:DepartmentPhoneProject');
                    }

                    if(!$callData['phone_from']) {
                        $callData['error'] = 'Not found source phone number for project ('.$leadModel->project->name.')';
                    }
                } else {
                    $callData['error'] = 'Not found agent phone number for project ('.$leadModel->project->name.')';
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

        if ($user->isAgent()) {
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

//
//        if(Yii::$app->user->identity->canRole('supervision')) {
//            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
//        }


        if ($user->isAdmin()) {
            $dataProvider = $searchModel->searchInbox($params, $user);
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
        $params['CallSearch']['c_created_user_id'] = $user->id;
        $params['CallSearch']['c_call_type_id'] = Call::CALL_TYPE_OUT;
        $params['CallSearch']['limit'] = 10;

        $dataProviderCall = $searchModelCall->searchAgent($params);
        $projectList = Project::getListByUser($user->id);


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

    /**
     * @return string
     */
    public function actionCallBox(): string
    {
        $id = Yii::$app->request->get('id');
        $status = Yii::$app->request->get('status');

        $keyCache = 'cal-box-request-' . $id . '-' . $status;

        //Yii::$app->cache->delete($keyCache);

        $result = Yii::$app->cache->get($keyCache);

        if($result === false) {

            $box = CallBox::getInstance();
            $result = $box->run();
            if($result) {
                Yii::$app->cache->set($keyCache, $result, 30);
            }
        }

        //VarDumper::dump($data); exit;

        return $result;
    }

    /**
     * @return string
     */
    public function actionIncomingCallWidget(): string
    {
        //$id = Yii::$app->request->get('id');
        // $status = Yii::$app->request->get('status');

        // $keyCache = 'cal-box-request-' . $id . '-' . $status;

        //Yii::$app->cache->delete($keyCache);

        //$result = Yii::$app->cache->get($keyCache);

        //if($result === false) {

            $box = IncomingCallWidget::getInstance();
            $result = $box->run();

            /*if($result) {
                Yii::$app->cache->set($keyCache, $result, 30);
            }*/
        //}

        //VarDumper::dump($data); exit;

        return $result;
    }


    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAjaxMissedCalls()
    {
        $searchModel = new CallSearch();

        $params = Yii::$app->request->queryParams;
        $params['CallSearch']['c_created_user_id'] = Yii::$app->user->id;
        $params['CallSearch']['c_call_type_id'] = Call::CALL_TYPE_IN;
        // $params['CallSearch']['c_call_status'] = Call::TW_STATUS_NO_ANSWER;
        $params['CallSearch']['c_status_id'] = Call::STATUS_NO_ANSWER;
        $params['CallSearch']['c_call_type_id'] = Call::CALL_TYPE_IN;

        $params['CallSearch']['limit'] = 20;
        //$params['CallSearch']['sort'] = false;

        $dataProvider = $searchModel->searchAgent($params);

        foreach ($dataProvider->models as $model) {
            if($model->c_is_new) {
                $model->c_is_new = false;
                $model->update(false);
            }
        }
        //$dataProvider->sort->so

        return $this->renderPartial('ajax_missed_calls', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionAjaxCallInfo()
    {
        $id = (int) Yii::$app->request->post('id');

        $model = $this->findModel($id);
        $this->checkAccess($model);

        if($model->c_is_new) {
            //$model->c_read_dt = date('Y-m-d H:i:s');
            $model->c_is_new = false;
            $model->update();
        }

        return $this->renderAjax('ajax_call_info', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionCancelManual(): Response
    {
        if (!$callId = (int)Yii::$app->request->post('id')) {
            throw new BadRequestHttpException();
        }

        try {
            $this->callService->cancelByCrash($callId, Yii::$app->user->id);
            return $this->asJson(['success' => true]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionAjaxCallCancel()
    {
        $id = (int) Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $this->checkAccess($model);

        if ($model->isStatusRinging() || $model->isStatusInProgress() || $model->isStatusQueue()) {
            $model->setStatusFailed();
            $model->update();
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCancel()
    {
        $id = (int) Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if ($result = $model->cancelCall()) {
            Yii::$app->session->setFlash('success', '<strong>Cancel Call</strong> Success');
        } else {
            Yii::$app->session->setFlash('error', '<strong>Cancel Call</strong> Error');
        }

        return $this->redirect(['index']);
    }

    public function actionAjaxGetCallHistory()
	{
		if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

			$userPhoneList = UserProjectParams::find()
				->select(['pl_phone_number'])
				->byUserId(Auth::id())
				->withExistingPhoneInPhoneList()
				->asArray()
				->all();

			$userPhoneList = ArrayHelper::getColumn($userPhoneList, 'pl_phone_number');

			$callSearch = new CallSearch();
			$page = Yii::$app->request->post('page', 0)+1;

			$params['CallSearch']['phoneList'] = $userPhoneList;
			$callHistory = $callSearch->getCallHistory($params);
			$callHistory->pagination->setPage($page);

			$rows = $callHistory->getModels();

			$result = [
				'html'  => $this->renderAjax('partial/_ajax_wg_call_history', [
					'callHistory' => CallHelper::formatCallHistoryByDate($rows),
				]),
				'page' => $page,
				'rows' => empty($rows)
			];

			return $this->asJson($result);

		}

		throw new BadRequestHttpException();
	}

    /**
     * @param string $sid
     * @param bool $cfRecord
     * @return string
     *
     * Potentially will be used future
     *
     */
    /*public function actionRecord(string $sid, bool $cfRecord = false):string
    {
        if (!$cfRecord){
            $recordUrl = Call::find()->select(['c_recording_url'])->where(['c_call_sid' => $sid])->one();
            $url = $recordUrl->c_recording_url;
        } else {
            $recordUrl = Conference::find()->select(['cf_recording_url'])->where(['cf_sid' => $sid])->one();
            $url = $recordUrl->cf_recording_url;
        }

        try {
            $twilioHeaders = array_change_key_case(get_headers($url, 1));
            Yii::$app->response->format = Response::FORMAT_RAW;
            $headers = Yii::$app->response->headers;
            $headers->add('Accept-Ranges', 'bytes');
            $headers->add('Content-Type', 'audio/x-wav');
            if(isset($twilioHeaders['content-length'])){
                $headers->add('Content-Length', $twilioHeaders['content-length']);
            }

            return file_get_contents($url);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }*/
}
