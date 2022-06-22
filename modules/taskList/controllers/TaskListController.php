<?php

namespace modules\taskList\controllers;

use common\components\bootstrap4\activeForm\ActiveForm;
use frontend\controllers\FController;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\taskList\search\TaskListSearch;
use modules\taskList\src\forms\TaskListAssignForm;
use modules\taskList\src\services\TaskListService;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TaskListController implements the CRUD actions for TaskList model.
 */
class TaskListController extends FController
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
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all TaskList models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TaskListSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAssignForm(): Response
    {
        $result = ['message' => '', 'status' => 0, 'data' => ''];
        $modelForm = new TaskListAssignForm();

        if ($modelForm->load(Yii::$app->request->post(), '')) {
            $data = (array) Yii::$app->request->post();

            try {
                if (!$modelForm->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($modelForm));
                }

                $modelForm->objectSegmentIds = ObjectSegmentTask::getAssignedObjectSegmentIdsByTaskId($modelForm->taskListId);

                $result['status'] = 1;
                $result['data'] = $this->renderAjax('assign_form', [
                    'model' => $modelForm,
                    'taskList' => TaskList::findOne(['tl_id' => $modelForm->taskListId]),
                ]);
            } catch (\RuntimeException | \DomainException $throwable) {
                $result['message'] = $throwable->getMessage();
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::warning($message, 'TaskListController:actionAssignForm:Exception');
            } catch (\Throwable $throwable) {
                $result['message'] = 'Internal Server Error';
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $data);
                \Yii::error($message, 'TaskListController:actionAssignForm:Throwable');
            }
        }

        return $this->asJson($result);
    }

    public function actionAssignValidation(): Response
    {
        try {
            $objectSegmentListAssignForm = new TaskListAssignForm();

            if ($objectSegmentListAssignForm->load(Yii::$app->request->post())) {
                return $this->asJson(
                    ActiveForm::validate($objectSegmentListAssignForm)
                );
            }
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'TaskListController:actionAssignValidation');
        }

        throw new BadRequestHttpException();
    }

    public function actionAssign(): Response
    {
        $result = ['message' => '', 'status' => 0];
        $objectSegmentListAssignForm = new TaskListAssignForm();
        $postData = (array) Yii::$app->request->post();

        try {
            if ($objectSegmentListAssignForm->load($postData)) {
                if ($objectSegmentListAssignForm->validate() === false) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($objectSegmentListAssignForm));
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    ObjectSegmentTask::deleteOrAddObjectSegments(
                        $objectSegmentListAssignForm->taskListId,
                        $objectSegmentListAssignForm->objectSegmentIds
                    );
                    $transaction->commit();

                    $result['status'] = 1;
                    $result['message'] = 'Object Segments assigned';
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    $result['message'] = $e->getMessage();
                    $message = ArrayHelper::merge(AppHelper::throwableLog($e), $postData);
                    Yii::warning($message, 'TaskListController:actionAssign:Exception');
                }
            } else {
                throw new \RuntimeException('TaskListAssignForm not loaded');
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $result['message'] = $throwable->getMessage();
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $postData);
            Yii::warning($message, 'TaskListController:actionAssign:Exception');
        } catch (\Throwable $throwable) {
            $result['message'] = 'Internal Server Error';
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $postData);
            Yii::error($message, 'TaskListController:actionAssign:Throwable');
        }

        return $this->asJson($result);
    }

    /**
     * Displays a single TaskList model.
     * @param int $tl_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($tl_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($tl_id),
        ]);
    }

    /**
     * Creates a new TaskList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new TaskList();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'tl_id' => $model->tl_id]);
            }
        } else {
            $model->loadDefaultValues();
            $model->tl_cron_expression = '* * * * *';
            $model->tl_enable_type = TaskList::ET_ENABLED;
            $model->tl_object = \Yii::$app->request->get('object');
            $model->tl_params_json = TaskListService::getDefaultOptionDataByObject($model->tl_object);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TaskList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $tl_id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws InvalidConfigException
     */
    public function actionUpdate($tl_id)
    {
        $model = $this->findModel($tl_id);

        // VarDumper::dump($model->tl_params_json, 10, true);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tl_id' => $model->tl_id]);
        } else {
            if (\Yii::$app->request->get('object')) {
                $model->tl_object = \Yii::$app->request->get('object');
            }
            $model->tl_params_json = $model->tl_params_json ?:
                TaskListService::getDefaultOptionDataByObject($model->tl_object);
            //exit;
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TaskList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $tl_id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($tl_id)
    {
        $this->findModel($tl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TaskList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $tl_id ID
     * @return TaskList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($tl_id)
    {
        if (($model = TaskList::findOne(['tl_id' => $tl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
