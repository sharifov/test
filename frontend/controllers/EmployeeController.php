<?php

namespace frontend\controllers;

use backend\models\search\EmployeeForm;
use common\controllers\DefaultController;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\ProjectEmployeeAccess;
use common\models\search\EmployeeSearch;
use common\models\UserGroupAssign;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Yii;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class EmployeeController extends DefaultController
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
                        'actions' => ['list', 'update', 'acl-rule'],
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
            throw new AccessDeniedException('Not found roles');
        } elseif (!in_array('admin', $roles) && Yii::$app->user->identity->getId() != $employeeId) {
            throw new AccessDeniedException('AccessDenied ('.$employeeId.')');
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
     * @throws BadRequestHttpException
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {
        $this->view->title = sprintf('Employees - Profile');

        $id = Yii::$app->request->get('id');
        if ($id !== null) {
            $model = Employee::findOne(['id' => $id]);

            if (!$model) {
                throw new NotFoundHttpException('The requested user does not exist.');
            }

            $roles = array_keys($model->getRoles());

            if(!Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) && in_array('admin', $roles)) {
                throw new NotAcceptableHttpException('Access denied for this user: '.$model->id);
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
                    throw new NotAcceptableHttpException('Access denied for this user (invalid user group)');
                }
            }



        } else {
            $model = new Employee(['scenario' => Employee::SCENARIO_REGISTER]);
        }

        if ($model !== null) {
            if (Yii::$app->request->isPost) {
                $attr = Yii::$app->request->post($model->formName());

                $availableProjects = isset($attr['viewItemsEmployeeAccess'])
                    ? array_keys(json_decode($attr['viewItemsEmployeeAccess'], true))
                    : [];
                $newEmployeeAccess = (isset($attr['employeeAccess']) && !empty($attr['employeeAccess']))
                    ? $attr['employeeAccess'] : [];

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
                    //VarDumper::dump($attr['user_groups'], 10, true); exit;

                    foreach ($availableProjects as $availableProject) {
                        if (!in_array($availableProject, $newEmployeeAccess) && in_array($availableProject, $model->employeeAccess)) {
                            ProjectEmployeeAccess::deleteAll([
                                'employee_id' => $model->id,
                                'project_id' => $availableProject
                            ]);
                        } else if (in_array($availableProject, $newEmployeeAccess) && !in_array($availableProject, $model->employeeAccess)) {
                            $access = new ProjectEmployeeAccess();
                            $access->employee_id = $model->id;
                            $access->project_id = $availableProject;
                            $access->save();
                        }
                    }
                    //$model = Employee::findOne(['id' => $id]);
                    Yii::$app->getSession()->setFlash('success', ($isNew) ? 'Profile created!' : 'Profile updated!');

                    if($isNew){
                        return $this->redirect(['update','id' => $model->id]);
                    }
                }
            }

            //VarDumper::dump($model->userGroupAssigns, 10 ,true); exit;

            $model->user_groups = ArrayHelper::map($model->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');

            return $this->render('_form', [
                'model' => $model,
                'isProfile' => false
            ]);
        }

        throw new BadRequestHttpException('"Employee ID ' . $id . '" not found.');
    }
}
