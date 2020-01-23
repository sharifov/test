<?php

namespace frontend\controllers;

use common\models\Employee;
use frontend\models\search\AirlineForm;
use frontend\models\search\AirportForm;
use frontend\models\search\GlobalAclForm;
use frontend\models\search\LogForm;
use common\controllers\DefaultController;
use common\models\GlobalAcl;
use common\models\Log;
use common\models\Project;
use common\models\ProjectEmailTemplate;
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
class SettingsController extends FController
{

    public function actionSync($type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success' => false
        ];

        switch ($type) {
            case 'projects':
                exec(dirname(Yii::getAlias('@app')) . '/yii sync/projects  > /dev/null');
                $response['success'] = true;
                break;
            case 'airlines':
                exec(dirname(Yii::getAlias('@app')) . '/yii sync/airlines  > /dev/null');
                $response['success'] = true;
                break;
            case 'airports':
                exec(dirname(Yii::getAlias('@app')) . '/yii sync/airports  > /dev/null');
                $response['success'] = true;
                break;
        }

        return $response;
    }


    /**
     * @return Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionSynchronization() : Response
    {
        $result = Project::synchronizationProjects();

        if($result) {
            if($result['error']) {
                Yii::$app->getSession()->setFlash('error', $result['error']);
            } else {
                $message = 'Synchronization successful<br>';
                if($result['created']) {
                    $message .= 'Created projects: "'.implode(', ', $result['created']).'"<br>';
                }
                if($result['updated']) {
                    $message .= 'Updated projects: "'.implode(', ', $result['updated']).'"<br>';
                }
                Yii::$app->getSession()->setFlash('success', $message);
            }
        }

        return $this->redirect(['settings/projects']);
    }



    public function actionAcl()
    {
        $this->view->title = sprintf('Global ACL - List');

        $searchModel = new GlobalAclForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new GlobalAcl();

        return $this->render('acls', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    public function actionAclRule($id = 0)
    {
        if (empty($id)) {
            $model = new GlobalAcl();
        } else {
            $model = GlobalAcl::findOne(['id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post($model->formName());
            $model->attributes = $attr;
            if (!Yii::$app->request->isAjax) {
                ($model->validate() && $model->save());
            } else {
                return $model->save();
            }
        }
        return $this->redirect(['settings/acl']);
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

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $availableProjects = [];
        if ($user->isAdmin()) {
            $query = Project::find();
        } else {
            $availableProjects = ArrayHelper::map(Yii::$app->user->identity->projectEmployeeAccesses, 'project_id', 'project_id');
            $query = Project::find()->where(['id' => $availableProjects]);
        }

        $projectId = Yii::$app->request->get('id', null);
        if ($projectId !== null) {
            $project = Project::findOne(['id' => $projectId]);
            if ($project !== null) {
                if (!$user->isAdmin() && !in_array($project->id, $availableProjects)) {
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

    public function actionProjectData($id)
    {
        $errors = [];
        $project = Project::findOne(['id' => $id]);
        if ($project !== null) {

            if (Yii::$app->request->isPost || Yii::$app->request->isAjax) {
                if ($project->load(Yii::$app->request->post())) {
                    if($project->save()){
                        return $this->redirect(['settings/projects']);
                    }
                }else{
                    if(empty($project->custom_data)){
                        $project->custom_data = @json_encode(['background-color' => '#f4f7fa', 'color' => '#000000']);
                    }
                }

                return $this->renderAjax('_project_custom_data', [
                    'model' => $project
                ]);
            }
        }
        return null;
    }


    /**
     * @return string
     */
    public function actionAirlines()
    {
        $this->view->title = sprintf('Airlines - List');

        $searchModel = new AirlineForm();

        return $this->render('airlines', [
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return string
     */
    public function actionAirports()
    {
        $this->view->title = sprintf('Airports - List');

        $searchModel = new AirportForm();

        return $this->render('airports', [
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return string
     */
    public function actionLogging()
    {
        $this->view->title = sprintf('Logging - List');

        $searchModel = new LogForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('logs', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewLog($id, $delete = false)
    {
        $model = Log::findOne($id);

        if ($delete) {
            $model->delete();
            return $this->redirect(['settings/logging']);
        } else {
            return $this->render('item/log', [
                'model' => $model,
            ]);
        }
    }
}
