<?php

namespace modules\objectTask\controllers;

use frontend\controllers\FController;
use modules\objectTask\src\abac\ObjectTaskObject;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskSearch;
use modules\objectTask\src\entities\repositories\ObjectTaskRepository;
use modules\objectTask\src\forms\ObjectTaskMultipleUpdateForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ObjectTaskCrudController implements the CRUD actions for ObjectTask model.
 */
class ObjectTaskCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_LIST, ObjectTaskObject::ACTION_ACCESS, Access to page object-task/object-task-crud/(index|view) */
                        [
                            'actions' => ['index', 'view'],
                            'allow' => \Yii::$app->abac->can(
                                null,
                                ObjectTaskObject::ACT_OBJECT_TASK_LIST,
                                ObjectTaskObject::ACTION_ACCESS
                            ),
                            'roles' => ['@'],
                        ],
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_LIST, ObjectTaskObject::ACTION_UPDATE, Access to page object-task/object-task-crud/update */
                        [
                            'actions' => ['update'],
                            'allow' => \Yii::$app->abac->can(
                                null,
                                ObjectTaskObject::ACT_OBJECT_TASK_LIST,
                                ObjectTaskObject::ACTION_UPDATE
                            ),
                            'roles' => ['@'],
                        ],
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_LIST, ObjectTaskObject::ACTION_UPDATE, Access to page object-task/object-task-crud/delete */
                        [
                            'actions' => ['delete'],
                            'allow' => \Yii::$app->abac->can(
                                null,
                                ObjectTaskObject::ACT_OBJECT_TASK_LIST,
                                ObjectTaskObject::ACTION_DELETE
                            ),
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionIndex(?string $act = null)
    {
        $multipleUpdateForm = new ObjectTaskMultipleUpdateForm();
        $multipleErrors = [];
        $searchModel = new ObjectTaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($multipleUpdateForm->load(Yii::$app->request->post()) && $multipleUpdateForm->validate()) {
            foreach ($multipleUpdateForm->element_list as $uuid) {
                $needSaveObjectTask = false;
                $objectTask = ObjectTask::find()
                    ->where([
                        'ot_uuid' => $uuid
                    ])
                    ->limit(1)
                    ->one();

                if (!empty($multipleUpdateForm->statusId) && $multipleUpdateForm->statusId !== $objectTask->ot_status) {
                    $needSaveObjectTask = true;
                    $objectTask->ot_status = $multipleUpdateForm->statusId;
                }

                if ($needSaveObjectTask === true) {
                    try {
                        (new ObjectTaskRepository($objectTask))->save();
                    } catch (\Throwable $e) {
                        $multipleErrors[$uuid] = $objectTask->getErrors();
                    }
                }
            }
        }

        if ($act === 'select-all') {
            $data = $searchModel->searchIds(Yii::$app->request->queryParams);

            return $this->asJson($data);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'multipleUpdateForm' => $multipleUpdateForm,
            'multipleErrors' => $multipleErrors,
        ]);
    }

    /**
     * Displays a single ObjectTask model.
     * @param string $ot_uuid Ot Uuid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ot_uuid)
    {
        return $this->render('view', [
            'model' => $this->findModel($ot_uuid),
        ]);
    }

    /**
     * Creates a new ObjectTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ObjectTask();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ot_uuid' => $model->ot_uuid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ObjectTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ot_uuid Ot Uuid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ot_uuid)
    {
        $model = $this->findModel($ot_uuid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ot_uuid' => $model->ot_uuid]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ObjectTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ot_uuid Ot Uuid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ot_uuid)
    {
        $this->findModel($ot_uuid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ot_uuid Ot Uuid
     * @return ObjectTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ot_uuid)
    {
        if (($model = ObjectTask::findOne(['ot_uuid' => $ot_uuid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
