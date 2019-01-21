<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\ProjectEmployeeAccess;
use common\models\search\EmployeeSearch;
use common\models\search\UserProjectParamsSearch;
use common\models\UserGroupAssign;
use common\models\UserParams;
use Yii;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class EmployeeController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['list', 'update', 'create', 'acl-rule'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['seller-contact-info'],
                        'allow' => true,
                        'roles' => ['agent', 'supervision'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionSellerContactInfo($employeeId)
    {
        $roles = Yii::$app->user->identity->getRoles();

        if(is_array($roles)) {
            $roles = array_keys($roles);
        }

        //print_r($roles); exit;

        if (empty($roles)) {
            throw new ForbiddenHttpException('Not found roles');
        } elseif (!in_array('admin', $roles) && Yii::$app->user->identity->getId() != $employeeId) {
            throw new ForbiddenHttpException('AccessDenied ('.$employeeId.')');
        }

        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [
                'success' => true,
                'errors' => []
            ];
            $errors = [];
            $attrArr = Yii::$app->request->post('EmployeeContactInfo');



            foreach ($attrArr as $key => $attr) {
                $model = empty($attr['id']) ? null : EmployeeContactInfo::findOne(['id' => $attr['id']]);

                if ($model === null) {
                    $model = new EmployeeContactInfo();
                }
                $model->attributes = $attr;



                if ($model->needSave()) {


                    if (!$model->save()) {

                        //print_r($model->errors); exit;

                        if ($model->hasErrors('email_user')) {
                            $errors[Html::getInputId($model, '[' . $key . ']email_user')] = true;
                        }
                        if ($model->hasErrors('email_pass')) {
                            $errors[Html::getInputId($model, '[' . $key . ']email_pass')] = true;
                        }
                        if ($model->hasErrors('direct_line')) {
                            $errors[Html::getInputId($model, '[' . $key . ']direct_line')] = true;
                        }

                        $errors[$key] = VarDumper::dumpAsString($model->getFirstErrors());

                    }
                }
            }

            if ($errors) {
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            return $result;
        }

        return null;
    }

    public function actionAclRule($id = 0)
    {
        if (empty($id)) {
            $model = new EmployeeAcl();
        } else {
            $model = EmployeeAcl::findOne(['id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post($model->formName());
            $model->attributes = $attr;
            if ($model->isNewRecord) {
                $success = $model->save();
                Yii::$app->response->format = Response::FORMAT_JSON;
                $employee = Employee::findOne(['id' => $model->employee_id]);
                return [
                    'body' => $this->renderAjax('partial/_aclList', [
                        'models' => $employee->employeeAcl,
                    ]),
                    'success' => $success
                ];
            } else {
                return $model->save();
            }
        }

        return null;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionList()
    {
        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['EmployeeSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {

        $model = new Employee(['scenario' => Employee::SCENARIO_REGISTER]);
        $modelUserParams = new UserParams();

        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post($model->formName());


            $isNew = $model->prepareSave($attr);
            if ($model->validate() && $model->save()) {

                $model->addRole($isNew);

                if(isset($attr['user_groups'])) {
                    if($attr['user_groups']) {
                        foreach ($attr['user_groups'] as $ugId) {
                            $uga = new UserGroupAssign();
                            $uga->ugs_user_id = $model->id;
                            $uga->ugs_group_id = (int) $ugId;
                            $uga->save();
                        }
                    }
                }


                if(isset($attr['user_projects'])) {
                    if($attr['user_projects']) {
                        foreach ($attr['user_projects'] as $ugId) {
                            $up = new ProjectEmployeeAccess();
                            $up->employee_id = $model->id;
                            $up->project_id = (int) $ugId;
                            $up->created = date('Y-m-d H:i:s');
                            $up->save();
                        }
                    }
                }


                Yii::$app->getSession()->setFlash('success', 'User created');


                if ($modelUserParams->load(Yii::$app->request->post())) {

                    //VarDumper::dump(Yii::$app->request->post()); exit;

                    $modelUserParams->up_user_id = $model->id;
                    $modelUserParams->up_updated_user_id = Yii::$app->user->id;

                    if($modelUserParams->save()) {
                        //return $this->refresh();
                    }
                }

                return $this->redirect(['update','id' => $model->id]);

            }
        } else {

            $modelUserParams->up_timezone = "Europe/Chisinau";
            $modelUserParams->up_work_minutes = 8 * 60;
            $modelUserParams->up_base_amount = 0;
            $modelUserParams->up_commission_percent = 0;

        }

        //VarDumper::dump($model->userGroupAssigns, 10 ,true); exit;

        //$model->user_groups = ArrayHelper::map($model->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');
        //$model->user_projects = ArrayHelper::map($model->projects, 'id', 'id');

        //VarDumper::dump($model->user_projects, 10, true); exit;


        $dataProvider = null;

        return $this->render('_form', [
            'model' => $model,
            'modelUserParams' => $modelUserParams,
            'dataProvider' => $dataProvider,
        ]);

    }


    /**
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {

        if ($id = Yii::$app->request->get('id')) {

            $model = Employee::findOne(['id' => $id]);

            if (!$model) {
                throw new NotFoundHttpException('The requested user does not exist.');
            }

            $modelUserParams = UserParams::findOne($model->id);
            if(!$modelUserParams) {
                $modelUserParams = new UserParams();
            }

            $roles = array_keys($model->getRoles());

            if(!Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) && in_array('admin', $roles)) {
                throw new ForbiddenHttpException('Access denied for this user: '.$model->id);
            }

            if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
                $access = false;

                $userGroups = array_keys($model->getUserGroupList());

                foreach (Yii::$app->user->identity->getUserGroupList() as $grId => $grName) {
                    if(in_array($grId, $userGroups)) {
                        $access = true;
                        break;
                    }
                }

                if(!$access) {
                    throw new ForbiddenHttpException('Access denied for this user (invalid user group)');
                }
            }

        } else {
            throw new BadRequestHttpException('Invalid request');
        }


            if (Yii::$app->request->isPost) {
                $attr = Yii::$app->request->post($model->formName());

                $isNew = $model->prepareSave($attr);
                if ($model->validate() && $model->save()) {


                    $model->addRole($isNew);


                    if(isset($attr['user_groups'])) {
                        UserGroupAssign::deleteAll(['ugs_user_id' => $model->id]);
                        if($attr['user_groups']) {
                            foreach ($attr['user_groups'] as $ugId) {
                                $uga = new UserGroupAssign();
                                $uga->ugs_user_id = $model->id;
                                $uga->ugs_group_id = (int) $ugId;
                                $uga->save();
                            }
                        }
                    }


                    if(isset($attr['user_projects'])) {
                        ProjectEmployeeAccess::deleteAll(['employee_id' => $model->id]);
                        if($attr['user_projects']) {
                            foreach ($attr['user_projects'] as $ugId) {
                                $up = new ProjectEmployeeAccess();
                                $up->employee_id = $model->id;
                                $up->project_id = (int) $ugId;
                                $up->created = date('Y-m-d H:i:s');
                                $up->save();
                            }
                        }
                    }

                    //VarDumper::dump($attr['user_groups'], 10, true); exit;


                    /*foreach ($availableProjects as $availableProject) {
                        if (!in_array($availableProject, $newEmployeeAccess) && in_array($availableProject, $model->user_projects)) {
                            ProjectEmployeeAccess::deleteAll([
                                'employee_id' => $model->id,
                                'project_id' => $availableProject
                            ]);
                        } else if (in_array($availableProject, $newEmployeeAccess) && !in_array($availableProject, $model->user_projects)) {
                            $access = new ProjectEmployeeAccess();
                            $access->employee_id = $model->id;
                            $access->project_id = $availableProject;
                            $access->save();
                        }
                    }*/

                    //$model = Employee::findOne(['id' => $id]);
                    Yii::$app->getSession()->setFlash('success', 'User updated');


                }
            }

            //VarDumper::dump($model->userGroupAssigns, 10 ,true); exit;

            $model->user_groups = ArrayHelper::map($model->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');
            $model->user_projects = ArrayHelper::map($model->projects, 'id', 'id');

            //VarDumper::dump($model->user_projects, 10, true); exit;



            $searchModel = new UserProjectParamsSearch();
            $params = Yii::$app->request->queryParams;

            /*if(Yii::$app->authManager->getAssignment('supervision', $model->id)) {

            }*/

            $params['UserProjectParamsSearch']['upp_user_id'] = $model->id;

            $dataProvider = $searchModel->search($params);


            if ($modelUserParams->load(Yii::$app->request->post())) {

                //VarDumper::dump(Yii::$app->request->post()); exit;

                $modelUserParams->up_user_id = $model->id;
                $modelUserParams->up_updated_user_id = Yii::$app->user->id;

                if($modelUserParams->save()) {
                    return $this->refresh();
                }
            }


            return $this->render('_form', [
                'model' => $model,
                'modelUserParams' => $modelUserParams,
                //'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

    }
}
