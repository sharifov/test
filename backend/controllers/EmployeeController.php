<?php

namespace backend\controllers;

use backend\models\search\EmployeeForm;
use common\controllers\DefaultController;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\ProjectEmployeeAccess;
use Yii;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
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
        return [
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
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];
    }

    public function actionSellerContactInfo($employeeId)
    {
        $roles = Yii::$app->user->identity->getRoles();
        if (empty($roles)) {
            return null;
        } elseif (!in_array('admin', $roles) && Yii::$app->user->identity->getId() != $employeeId) {
            return null;
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
                $model = empty($attr['id'])
                    ? null
                    : EmployeeContactInfo::findOne(['id' => $attr['id']]);
                if ($model === null) {
                    $model = new EmployeeContactInfo();
                }
                $model->attributes = $attr;
                if ($model->needSave()) {
                    if (!$model->save()) {
                        if ($model->hasErrors('email_user')) {
                            $errors[Html::getInputId($model, '[' . $key . ']email_user')] = true;
                        }
                        if ($model->hasErrors('email_pass')) {
                            $errors[Html::getInputId($model, '[' . $key . ']email_pass')] = true;
                        }
                        if ($model->hasErrors('direct_line')) {
                            $errors[Html::getInputId($model, '[' . $key . ']direct_line')] = true;
                        }
                    }
                }
            }
            if (!empty($errors)) {
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
        $this->view->title = sprintf('Employees - List');
        $searchModel = new EmployeeForm();

        $employees = ArrayHelper::map(Employee::find()->orderBy('username')->asArray()->all(), 'id', 'username');

        return $this->render('list', [
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'searchModel' => $searchModel,
            'employees' => $employees
        ]);
    }


    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate()
    {
        $this->view->title = sprintf('Employees - Profile');

        $id = Yii::$app->request->get('id', null);
        if ($id !== null) {
            $model = Employee::findOne(['id' => $id]);
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
                    $model = Employee::findOne(['id' => $id]);
                    Yii::$app->getSession()->setFlash('success', ($isNew) ? 'Profile created!' : 'Profile updated!');
                }
            }

            return $this->render('_form', [
                'model' => $model,
                'isProfile' => false
            ]);
        }

        throw new BadRequestHttpException('"Employee ID ' . $id . '" not found.');
    }
}
