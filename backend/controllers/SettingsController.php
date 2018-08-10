<?php
namespace backend\controllers;

use backend\models\search\EmployeeForm;
use common\controllers\DefaultController;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\Project;
use common\models\ProjectEmailTemplate;
use common\models\ProjectEmployeeAccess;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SettingsController extends DefaultController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'projects', 'airlines', 'airports', 'logging', 'acl', 'email-template'
                        ],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ],
        ];
    }

    public function actionAcl($id = 0)
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

    public function actionEmailTemplate($id, $templateId)
    {
        $model = Project::findOne(['id' => $id]);
        if ($model !== null) {
            $types = ProjectEmailTemplate::getTypes();
            if (empty($templateId)) {
                $templateTypes = ArrayHelper::map(ProjectEmailTemplate::find()->where(['project_id' => $id])->asArray()->all(), 'id', 'type');
                $template = new ProjectEmailTemplate();
                $template->project_id = $id;
                $template->id = 0;

                foreach ($templateTypes as $templateType) {
                    unset($types[$templateType]);
                }
            } else {
                $template = ProjectEmailTemplate::findOne([
                    'id' => $templateId,
                    'project_id' => $id
                ]);
            }

            if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $result = [
                    'success' => false,
                    'error' => [],
                    'body' => ''
                ];

                $template->attributes = Yii::$app->request->post($template->formName());
                if ($template->save()) {
                    $result['success'] = true;
                } else {
                    $result['body'] = $this->renderAjax('modal/_emailTemplate', [
                        'model' => $model,
                        'template' => $template
                    ]);
                    $result['error'] = [
                        $template->getErrors(),
                        $model->getErrors()
                    ];
                }
                return $result;
            }

            return $this->renderAjax('modal/_emailTemplate', [
                'model' => $model,
                'types' => $types,
                'template' => $template
            ]);
        }
        return null;
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionProjects()
    {
        $this->view->title = sprintf('Projects - List');

        $availableProjects = [];
        if (Yii::$app->user->identity->role == 'admin') {
            $query = Project::find();
        } else {
            $availableProjects = ArrayHelper::map(Yii::$app->user->identity->projectEmployeeAccesses, 'project_id', 'project_id');
            $query = Project::find()->where(['id' => $availableProjects]);
        }

        $projectId = Yii::$app->request->get('id', null);
        if ($projectId !== null) {
            $project = Project::findOne(['id' => $projectId]);
            if ($project !== null) {
                if (Yii::$app->user->identity->role != 'admin' && !in_array($project->id, $availableProjects)) {
                    throw new ForbiddenHttpException();
                }
                return $this->render('item/project', [
                    'model' => $project,
                ]);
            } else {
                throw new BadRequestHttpException();
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('projects', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionAirlines()
    {
        $this->view->title = sprintf('Employees - Profile');

        return $this->render('list');
    }

    /**
     * @return string
     */
    public function actionAirports()
    {
        $this->view->title = sprintf('Employees - Profile');

        return $this->render('list');
    }

    /**
     * @return string
     */
    public function actionLogging()
    {
        $this->view->title = sprintf('Employees - Profile');

        return $this->render('list');
    }
}
